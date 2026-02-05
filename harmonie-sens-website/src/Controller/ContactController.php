<?php

namespace App\Controller;

use App\Entity\Message;
use App\Form\ContactType;
use App\Service\MicrosoftGraphService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Annotation\Route;
use Psr\Log\LoggerInterface;

class ContactController extends AbstractController
{
    public function __construct(
        private MicrosoftGraphService $graphService,
        private LoggerInterface $logger
    ) {}

    #[Route('/contact', name: 'app_contact')]
    public function index(Request $request, EntityManagerInterface $em, MailerInterface $mailer): Response
    {
        $message = new Message();
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
                $mailer->send($email);
            } catch (\Exception $e) {
                // Log l'erreur mais ne bloque pas l'utilisateur
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
        ]);
    }
}
