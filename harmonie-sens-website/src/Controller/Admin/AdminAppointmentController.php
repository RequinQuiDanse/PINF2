<?php

namespace App\Controller\Admin;

use App\Entity\Appointment;
use App\Repository\AppointmentRepository;
use App\Service\MicrosoftGraphService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[Route('/admin/appointments')]
class AdminAppointmentController extends AbstractController
{
    #[Route('/', name: 'admin_appointment_index')]
    public function index(AppointmentRepository $repository): Response
    {
        $appointments = $repository->findAllOrderedByDate();

        return $this->render('admin/appointments/list.html.twig', [
            'appointments' => $appointments,
        ]);
    }

    #[Route('/{id}', name: 'admin_appointment_view', requirements: ['id' => '\d+'])]
    public function view(Appointment $appointment): Response
    {
        return $this->render('admin/appointments/view.html.twig', [
            'appointment' => $appointment,
        ]);
    }

    #[Route('/confirm/{token}', name: 'admin_appointment_confirm')]
    public function confirm(
        string $token,
        AppointmentRepository $repository,
        EntityManagerInterface $em,
        MailerInterface $mailer,
        MicrosoftGraphService $graphService
    ): Response {
        $appointment = $repository->findByToken($token);

        if (!$appointment) {
            throw $this->createNotFoundException('Rendez-vous introuvable.');
        }

        if (!$appointment->isPending()) {
            $this->addFlash('warning', 'Ce rendez-vous a déjà été traité.');
            return $this->redirectToRoute('admin_appointment_index');
        }

        $appointment->setStatus(Appointment::STATUS_CONFIRMED);
        $appointment->setConfirmedAt(new \DateTimeImmutable());
        $em->flush();

        // Ajouter au calendrier Outlook + réunion Teams
        $teamsLink = $graphService->createCalendarEvent($appointment);

        // Envoyer email de confirmation au client avec lien Teams
        $email = (new TemplatedEmail())
            ->from(new Address('houss20119@gmail.com', 'Harmonie & Sens'))
            ->to($appointment->getEmail())
            ->subject('Votre rendez-vous est confirmé - Harmonie & Sens')
            ->htmlTemplate('emails/appointment_confirmed.html.twig')
            ->context([
                'appointment' => $appointment,
                'teams_link' => $teamsLink,
            ]);

        try {
            $mailer->send($email);
        } catch (\Exception $e) {
        }

        $message = 'Le rendez-vous a été confirmé et un email a été envoyé à ' . $appointment->getEmail();
        if ($teamsLink) {
            $message .= ' | Ajouté au calendrier Outlook avec lien Teams.';
        } else {
            $message .= ' | Attention : impossible de créer la réunion Teams.';
        }

        $this->addFlash('success', $message);
        return $this->redirectToRoute('admin_appointment_index');
    }

    #[Route('/reject/{token}', name: 'admin_appointment_reject', methods: ['GET', 'POST'])]
    public function reject(
        string $token,
        Request $request,
        AppointmentRepository $repository,
        EntityManagerInterface $em,
        MailerInterface $mailer
    ): Response {
        $appointment = $repository->findByToken($token);

        if (!$appointment) {
            throw $this->createNotFoundException('Rendez-vous introuvable.');
        }

        if (!$appointment->isPending()) {
            $this->addFlash('warning', 'Ce rendez-vous a déjà été traité.');
            return $this->redirectToRoute('admin_appointment_index');
        }

        // Si POST, on traite le refus
        if ($request->isMethod('POST')) {
            $reason = $request->request->get('reason', '');

            $appointment->setStatus(Appointment::STATUS_REJECTED);
            $appointment->setRejectedAt(new \DateTimeImmutable());
            $appointment->setRejectionReason($reason);
            $em->flush();

            // Envoyer email de refus au client avec options
            $rescheduleUrl = $this->generateUrl('app_contact', ['tab' => 'appointment'], UrlGeneratorInterface::ABSOLUTE_URL);

            $email = (new TemplatedEmail())
                ->from(new Address('houss20119@gmail.com', 'Harmonie & Sens'))
                ->to($appointment->getEmail())
                ->subject('Concernant votre demande de rendez-vous - Harmonie & Sens')
                ->htmlTemplate('emails/appointment_rejected.html.twig')
                ->context([
                    'appointment' => $appointment,
                    'reason' => $reason,
                    'reschedule_url' => $rescheduleUrl,
                ]);

            try {
                $mailer->send($email);
            } catch (\Exception $e) {
            }

            $this->addFlash('success', 'Le rendez-vous a été refusé et un email a été envoyé à ' . $appointment->getEmail());
            return $this->redirectToRoute('admin_appointment_index');
        }

        // GET : afficher le formulaire de refus
        return $this->render('admin/appointments/reject.html.twig', [
            'appointment' => $appointment,
        ]);
    }

    #[Route('/{id}/delete', name: 'admin_appointment_delete', methods: ['POST'])]
    public function delete(Request $request, Appointment $appointment, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete' . $appointment->getId(), $request->request->get('_token'))) {
            $em->remove($appointment);
            $em->flush();
            $this->addFlash('success', 'Rendez-vous supprimé avec succès');
        }

        return $this->redirectToRoute('admin_appointment_index');
    }
}
