<?php

namespace App\Controller;

use App\Entity\Appointment;
use App\Entity\Message;
use App\Form\AppointmentType;
use App\Form\ContactType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'app_contact')]
    public function index(Request $request, EntityManagerInterface $em, MailerInterface $mailer): Response
    {
        $activeTab = 'message';

        // Formulaire de contact
        $message = new Message();
        $contactForm = $this->createForm(ContactType::class, $message);
        $contactForm->handleRequest($request);

        if ($contactForm->isSubmitted() && $contactForm->isValid()) {
            $em->persist($message);
            $em->flush();

            $email = (new TemplatedEmail())
                ->from(new Address('houss20119@gmail.com', 'Harmonie & Sens'))
                ->to('houss20119@gmail.com')
                ->subject('Nouveau message de contact - ' . $message->getSubject())
                ->htmlTemplate('emails/contact_notification.html.twig')
                ->context([
                    'message' => $message,
                ]);

            try {
                $mailer->send($email);
            } catch (\Exception $e) {
            }

            $this->addFlash('success', 'Votre message a été envoyé avec succès. Nous vous répondrons dans les plus brefs délais.');
            return $this->redirectToRoute('app_contact');
        }

        // Formulaire de rendez-vous
        $appointment = new Appointment();
        $appointmentForm = $this->createForm(AppointmentType::class, $appointment);
        $appointmentForm->handleRequest($request);

        if ($appointmentForm->isSubmitted() && $appointmentForm->isValid()) {
            $activeTab = 'appointment';
            $em->persist($appointment);
            $em->flush();

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
            } catch (\Exception $e) {
            }

            $this->addFlash('success', 'Votre demande de rendez-vous a été envoyée avec succès. Vous recevrez un email de confirmation sous 24h ouvrées.');
            return $this->redirectToRoute('app_contact', ['tab' => 'appointment']);
        }

        // Tab active depuis le query param
        if ($request->query->get('tab') === 'appointment') {
            $activeTab = 'appointment';
        }

        return $this->render('client/contact/index.html.twig', [
            'contactForm' => $contactForm,
            'appointmentForm' => $appointmentForm,
            'activeTab' => $activeTab,
        ]);
    }
}
