<?php

namespace App\Controller;

use App\Entity\Appointment;
use App\Form\AppointmentType;
use App\Repository\AppointmentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class AppointmentController extends AbstractController
{
    #[Route('/rendez-vous', name: 'app_appointment')]
    public function index(Request $request, EntityManagerInterface $em, MailerInterface $mailer): Response
    {
        $appointment = new Appointment();
        $form = $this->createForm(AppointmentType::class, $appointment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($appointment);
            $em->flush();

            // Envoyer un email de notification à l'admin avec les liens confirmer/refuser
            $confirmUrl = $this->generateUrl('admin_appointment_confirm', [
                'token' => $appointment->getToken()
            ], UrlGeneratorInterface::ABSOLUTE_URL);

            $rejectUrl = $this->generateUrl('admin_appointment_reject', [
                'token' => $appointment->getToken()
            ], UrlGeneratorInterface::ABSOLUTE_URL);

            $email = (new TemplatedEmail())
                ->from(new Address('houss20119@gmail.com', 'Harmonie & Sens'))
                ->to('houss20119@gmail.com')
                ->subject('Nouvelle demande de rendez-vous - ' . $appointment->getFullName())
                ->htmlTemplate('emails/appointment_admin_notification.html.twig')
                ->context([
                    'appointment' => $appointment,
                    'confirm_url' => $confirmUrl,
                    'reject_url' => $rejectUrl,
                ]);

            try {
                $mailer->send($email);
                $this->addFlash('success', 'Email envoyé avec succès à houss20119@gmail.com !');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Erreur email : ' . $e->getMessage());
            }

            $this->addFlash('success', 'Votre demande de rendez-vous a été envoyée avec succès. Vous recevrez un email de confirmation sous 24h ouvrées.');
            return $this->redirectToRoute('app_appointment');
        }

        return $this->render('client/appointment/index.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/api/available-slots', name: 'api_available_slots', methods: ['GET'])]
    public function availableSlots(Request $request, AppointmentRepository $repository): JsonResponse
    {
        $dateStr = $request->query->get('date');
        if (!$dateStr) {
            return new JsonResponse(['error' => 'Date manquante'], 400);
        }

        try {
            $date = new \DateTime($dateStr);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Date invalide'], 400);
        }

        $allSlots = ['09:00','09:30','10:00','10:30','11:00','11:30','14:00','14:30','15:00','15:30','16:00','16:30','17:00'];
        $takenSlots = $repository->findTakenTimeSlotsForDate($date);
        $availableSlots = array_values(array_diff($allSlots, $takenSlots));

        return new JsonResponse(['slots' => $availableSlots]);
    }
}
