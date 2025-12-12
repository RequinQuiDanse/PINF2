<?php

namespace App\Controller;

use App\Entity\Message;
use App\Form\ContactType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'app_contact')]
    public function index(Request $request, EntityManagerInterface $em, MailerInterface $mailer): Response
    {
        $message = new Message();
        $form = $this->createForm(ContactType::class, $message);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
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

            $this->addFlash('success', 'Votre message a été envoyé avec succès. Nous vous répondrons dans les plus brefs délais.');
            return $this->redirectToRoute('app_contact');
        }

        return $this->render('client/contact/index.html.twig', [
            'form' => $form,
        ]);
    }
}
