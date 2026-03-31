<?php

namespace App\Controller;

use App\Entity\Appointment;
use App\Entity\Message;
use App\Entity\Person;
use App\Form\AppointmentType;
use App\Form\ContactType;
use App\Repository\PersonRepository;
use App\Service\EmailService;
use App\Service\MicrosoftGraphService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ContactController extends AbstractController
{
    public function __construct(
        private MicrosoftGraphService $graphService,
        private LoggerInterface $logger
    ) {
    }

    #[Route('/contact', name: 'app_contact')]
    public function index(
        Request $request,
        EntityManagerInterface $em,
        EmailService $emailService,
        PersonRepository $personRepository
    ): Response {
        $activeTab = $request->query->get('tab') === 'appointment' ? 'appointment' : 'message';

        $message = new Message();
        $quoteData = $this->buildQuoteData($request, $message);

        $form = $this->createForm(ContactType::class, $message);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $appointmentDateStr = $form->get('appointmentDate')->getData();
            $appointmentDuration = $form->get('appointmentDuration')->getData() ?: '30';

            if ($message->getRequestType() === 'appointment' && $appointmentDateStr) {
                $this->handleMessageAppointment($message, (string) $appointmentDateStr, (string) $appointmentDuration);
            }

            try {
                $person = $this->findOrCreatePerson($message, $personRepository);
                $em->persist($person);
                $em->persist($message);
                $em->flush();

                $emailSent = $emailService->sendContactNotification($message);
                $confirmationSent = $emailService->sendContactConfirmation($message);

                if ($message->getRequestType() === 'appointment' && $message->getAppointmentDate()) {
                    $this->addFlash('success', sprintf(
                        'Votre demande de rendez-vous pour le %s a ete enregistree. Vous recevrez une confirmation par email.',
                        $message->getAppointmentDate()->format('d/m/Y a H:i')
                    ));
                } elseif ($emailSent && $confirmationSent) {
                    $this->addFlash('success', 'Votre message a ete envoye avec succes. Un email de confirmation vous a ete envoye.');
                } elseif ($emailSent) {
                    $this->addFlash('success', 'Votre message a ete envoye avec succes.');
                } else {
                    $this->addFlash('warning', 'Votre message a ete enregistre mais l\'email de notification n\'a pas pu etre envoye.');
                }
            } catch (\Exception $e) {
                $this->logger->error('Erreur enregistrement message contact', [
                    'error' => $e->getMessage(),
                ]);
                $this->addFlash('error', 'Une erreur est survenue lors de l\'envoi de votre message.');
            }

            return $this->redirectToRoute('app_contact');
        }

        $appointment = new Appointment();
        $appointmentForm = $this->createForm(AppointmentType::class, $appointment);
        $appointmentForm->handleRequest($request);

        if ($appointmentForm->isSubmitted() && $appointmentForm->isValid()) {
            $activeTab = 'appointment';
            $em->persist($appointment);
            $em->flush();

            $confirmUrl = $this->generateUrl('admin_appointment_confirm', [
                'token' => $appointment->getToken(),
            ], UrlGeneratorInterface::ABSOLUTE_URL);

            $rejectUrl = $this->generateUrl('admin_appointment_reject', [
                'token' => $appointment->getToken(),
            ], UrlGeneratorInterface::ABSOLUTE_URL);

            $emailService->sendTemplatedEmail(
                'houss20119@gmail.com',
                'Nouvelle demande de rendez-vous - ' . $appointment->getFullName(),
                'emails/appointment_admin_notification.html.twig',
                [
                    'appointment' => $appointment,
                    'confirm_url' => $confirmUrl,
                    'reject_url' => $rejectUrl,
                ]
            );

            $this->addFlash('success', 'Votre demande de rendez-vous a ete envoyee avec succes.');

            return $this->redirectToRoute('app_contact', ['tab' => 'appointment']);
        }

        return $this->render('client/contact/index.html.twig', [
            'appointmentForm' => $appointmentForm,
            'activeTab' => $activeTab,
            'form' => $form,
            'quoteData' => $quoteData,
        ]);
    }

    private function buildQuoteData(Request $request, Message $message): ?array
    {
        $quoteService = $request->query->get('service');
        $quoteDuration = $request->query->get('duration');
        $quoteUnit = $request->query->get('unit');
        $quoteEstimation = $request->query->get('estimation');

        if (!$quoteService || !$quoteEstimation) {
            return null;
        }

        $quoteData = [
            'service' => $quoteService,
            'duration' => $quoteDuration,
            'unit' => $quoteUnit,
            'estimation' => $quoteEstimation,
        ];

        $message->setSubject('Demande de devis detaille - ' . $quoteService);

        $messageContent = "Bonjour,\n\n";
        $messageContent .= "Suite a mon estimation en ligne, je souhaiterais obtenir un devis detaille pour :\n\n";
        $messageContent .= "Service : " . $quoteService . "\n";
        if ($quoteDuration && $quoteUnit) {
            $messageContent .= "Duree souhaitee : " . $quoteDuration . ' ' . $quoteUnit . "\n";
        }
        $messageContent .= "Estimation : " . $quoteEstimation . "\n\n";
        $messageContent .= "Pourriez-vous me recontacter pour discuter de mon projet et etablir un devis personnalise ?\n\n";
        $messageContent .= 'Cordialement.';

        $message->setMessage($messageContent);

        return $quoteData;
    }

    private function handleMessageAppointment(Message $message, string $appointmentDateStr, string $appointmentDuration): void
    {
        try {
            $appointmentDate = new \DateTimeImmutable($appointmentDateStr);
            $durationMinutes = $appointmentDuration === '60' ? 60 : 30;
            $appointmentEndDate = $appointmentDate->modify("+{$durationMinutes} minutes");

            $message->setAppointmentDate($appointmentDate);
            $message->setAppointmentEndDate($appointmentEndDate);
            $message->setAppointmentStatus('pending');

            $eventDescription = sprintf(
                '<p><strong>Demande de rendez-vous</strong></p><p>Client: %s %s</p><p>Email: %s</p><p>Telephone: %s</p><p>Sujet: %s</p><p>Message: %s</p>',
                (string) $message->getFirstName(),
                (string) $message->getLastName(),
                (string) $message->getEmail(),
                (string) ($message->getPhone() ?? 'Non renseigne'),
                (string) $message->getSubject(),
                nl2br((string) $message->getMessage())
            );

            $event = $this->graphService->createCalendarEventFromData(
                'RDV - ' . $message->getFirstName() . ' ' . $message->getLastName(),
                $appointmentDate,
                $appointmentEndDate,
                $eventDescription,
                [(string) $message->getEmail()]
            );

            if ($event && isset($event['id'])) {
                $message->setMicrosoftEventId($event['id']);
                $message->setAppointmentStatus('confirmed');
            }
        } catch (\Exception $e) {
            $this->logger->error('Erreur creation rendez-vous via formulaire contact', [
                'error' => $e->getMessage(),
                'email' => $message->getEmail(),
            ]);
            $message->setAppointmentStatus('pending');
        }
    }

    private function findOrCreatePerson(Message $message, PersonRepository $personRepository): Person
    {
        $person = $personRepository->findOneByEmail($message->getEmail());

        if ($person) {
            if ($message->getPhone() && $message->getPhone() !== $person->getPhone()) {
                $person->setPhone($message->getPhone());
            }

            return $person;
        }

        $person = new Person();
        $person->setFirstName((string) $message->getFirstName());
        $person->setLastName((string) $message->getLastName());
        $person->setEmail((string) $message->getEmail());
        $person->setPhone($message->getPhone());

        return $person;
    }
}
