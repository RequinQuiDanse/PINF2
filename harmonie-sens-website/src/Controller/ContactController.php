<?php

namespace App\Controller;

use App\Entity\Message;
use App\Entity\Person;
use App\Form\ContactType;
use App\Service\MicrosoftGraphService;
use App\Repository\PersonRepository;
use App\Service\EmailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Psr\Log\LoggerInterface;

class ContactController extends AbstractController
{
    public function __construct(
        private MicrosoftGraphService $graphService,
        private LoggerInterface $logger
    ) {}

    #[Route('/contact', name: 'app_contact')]
    public function index(
        Request $request, 
        EntityManagerInterface $em, 
        EmailService $emailService,
        PersonRepository $personRepository
    ): Response
    {
        $message = new Message();
        
        // Récupérer les données du devis si elles existent dans les paramètres GET
        $quoteData = null;
        $quoteService = $request->query->get('service');
        $quoteDuration = $request->query->get('duration');
        $quoteUnit = $request->query->get('unit');
        $quoteEstimation = $request->query->get('estimation');
        
        if ($quoteService && $quoteEstimation) {
            $quoteData = [
                'service' => $quoteService,
                'duration' => $quoteDuration,
                'unit' => $quoteUnit,
                'estimation' => $quoteEstimation,
            ];
            
            // Pré-remplir le sujet et le message avec les infos du devis
            $message->setSubject('Demande de devis détaillé - ' . $quoteService);
            
            $messageContent = "Bonjour,\n\n";
            $messageContent .= "Suite à mon estimation en ligne, je souhaiterais obtenir un devis détaillé pour :\n\n";
            $messageContent .= "📋 Service : " . $quoteService . "\n";
            if ($quoteDuration && $quoteUnit) {
                $messageContent .= "⏱️ Durée souhaitée : " . $quoteDuration . " " . $quoteUnit . "\n";
            }
            $messageContent .= "💰 Estimation : " . $quoteEstimation . "\n\n";
            $messageContent .= "Pourriez-vous me recontacter pour discuter de mon projet et établir un devis personnalisé ?\n\n";
            $messageContent .= "Cordialement.";
            
            $message->setMessage($messageContent);
        }
        
        $form = $this->createForm(ContactType::class, $message);
        $form->handleRequest($request);

        // Debug: afficher si le formulaire a été soumis
        if ($request->isMethod('POST')) {
            $this->logger->info('Formulaire contact reçu', [
                'submitted' => $form->isSubmitted(),
                'valid' => $form->isSubmitted() ? $form->isValid() : 'non soumis',
                'requestType' => $message->getRequestType(),
                'appointmentDate' => $request->request->all()['contact']['appointmentDate'] ?? 'vide'
            ]);
        }

        if ($form->isSubmitted() && $form->isValid()) {
            // Gérer le rendez-vous si demandé
            $appointmentDateStr = $request->request->all()['contact']['appointmentDate'] ?? null;
            $appointmentDuration = $request->request->all()['contact']['appointmentDuration'] ?? '30';
            
            if ($message->getRequestType() === 'appointment' && $appointmentDateStr) {
                $appointmentDate = new \DateTimeImmutable($appointmentDateStr);
                $durationMinutes = $appointmentDuration === '60' ? 60 : 30;
                $appointmentEndDate = $appointmentDate->modify("+{$durationMinutes} minutes");
                
                $message->setAppointmentDate($appointmentDate);
                $message->setAppointmentEndDate($appointmentEndDate);
                $message->setAppointmentStatus('pending');
                
                // Créer l'événement dans le calendrier Microsoft
                try {
                    $eventDescription = sprintf(
                        "<p><strong>Demande de rendez-vous</strong></p>
                        <p>Client: %s %s</p>
                        <p>Email: %s</p>
                        <p>Téléphone: %s</p>
                        <p>Sujet: %s</p>
                        <p>Message: %s</p>",
                        $message->getFirstName(),
                        $message->getLastName(),
                        $message->getEmail(),
                        $message->getPhone() ?? 'Non renseigné',
                        $message->getSubject(),
                        nl2br($message->getMessage())
                    );
                    
                    $event = $this->graphService->createCalendarEvent(
                        'RDV - ' . $message->getFirstName() . ' ' . $message->getLastName(),
                        $appointmentDate,
                        $appointmentEndDate,
                        $eventDescription,
                        [$message->getEmail()]
                    );
                    
                    if ($event && isset($event['id'])) {
                        $message->setMicrosoftEventId($event['id']);
                        $message->setAppointmentStatus('confirmed');
                        $this->logger->info('Rendez-vous créé dans le calendrier', [
                            'eventId' => $event['id'],
                            'client' => $message->getEmail()
                        ]);
                    } else {
                        $this->logger->warning('Création événement échouée - pas d\'ID retourné', [
                            'response' => $event
                        ]);
                    }
                } catch (\Exception $e) {
                    $this->logger->error('Erreur création rendez-vous', [
                        'error' => $e->getMessage(),
                        'client' => $message->getEmail()
                    ]);
                    $message->setAppointmentStatus('pending');
                }
            }

            $em->persist($message);
            $em->flush();

            // Envoyer un email à l'admin
            $email = (new TemplatedEmail())
                ->from(new Address('noreply@harmonieetsens.fr', 'Harmonie & Sens'))
                ->to('contact@harmonieetsens.fr')
                ->subject('Nouveau message de contact - ' . $message->getSubject())
                ->htmlTemplate('emails/contact_notification.html.twig')
                ->context([
                    'message' => $message,
                ]);

            try {
                // Créer ou mettre à jour le profil Person
                $person = $this->findOrCreatePerson($message, $personRepository);
                $em->persist($person);
                
                // Sauvegarder le message en base de données
                $em->persist($message);
                $em->flush();

                // Envoyer un email de notification à l'administrateur
                $emailSent = $emailService->sendContactNotification($message);

                // Envoyer un email de confirmation à l'expéditeur
                $confirmationSent = $emailService->sendContactConfirmation($message);

                if ($emailSent && $confirmationSent) {
                    $this->addFlash('success', 'Votre message a été envoyé avec succès. Un email de confirmation vous a été envoyé. Nous vous répondrons dans les plus brefs délais.');
                } elseif ($emailSent) {
                    $this->addFlash('success', 'Votre message a été envoyé avec succès. Nous vous répondrons dans les plus brefs délais.');
                } else {
                    $this->addFlash('warning', 'Votre message a été enregistré mais l\'email de notification n\'a pas pu être envoyé. Nous traiterons votre demande dès que possible.');
                }
            } catch (\Exception $e) {
                $this->addFlash('error', 'Une erreur est survenue : ' . $e->getMessage());
            }

            if ($message->getRequestType() === 'appointment' && $message->getAppointmentDate()) {
                $this->addFlash('success', sprintf(
                    'Votre demande de rendez-vous pour le %s a été enregistrée. Vous recevrez une confirmation par email.',
                    $message->getAppointmentDate()->format('d/m/Y à H:i')
                ));
            } else {
                $this->addFlash('success', 'Votre message a été envoyé avec succès. Nous vous répondrons dans les plus brefs délais.');
            }
            
            return $this->redirectToRoute('app_contact');
        }

        return $this->render('client/contact/index.html.twig', [
            'form' => $form,
            'quoteData' => $quoteData,
        ]);
    }

    /**
     * Recherche ou crée une Person à partir des données du message
     * Gère le cas où le même email est utilisé par plusieurs personnes
     */
    private function findOrCreatePerson(Message $message, PersonRepository $personRepository): Person
    {
        // Chercher d'abord par email (identifiant principal)
        $person = $personRepository->findOneByEmail($message->getEmail());
        
        if ($person) {
            // La personne existe déjà avec cet email
            // Mettre à jour le téléphone si fourni et différent
            if ($message->getPhone() && $message->getPhone() !== $person->getPhone()) {
                $person->setPhone($message->getPhone());
            }
            // Mettre à jour le nom si différent (cas homonyme ou changement)
            // On ne met à jour que si les noms sont identiques pour éviter d'écraser
            // les données d'une autre personne utilisant le même email
            return $person;
        }
        
        // Si le téléphone est fourni, vérifier s'il existe déjà
        if ($message->getPhone()) {
            $personByPhone = $personRepository->findOneByPhone($message->getPhone());
            if ($personByPhone) {
                // Même téléphone mais email différent
                // On crée quand même une nouvelle personne car l'email est différent
                // (cas de plusieurs personnes dans un foyer avec le même numéro)
            }
        }
        
        // Créer une nouvelle personne
        $person = new Person();
        $person->setFirstName($message->getFirstName());
        $person->setLastName($message->getLastName());
        $person->setEmail($message->getEmail());
        $person->setPhone($message->getPhone());
        
        return $person;
    }
}
