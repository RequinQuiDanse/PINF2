<?php

namespace App\Service;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Psr\Log\LoggerInterface;

/**
 * Service centralisé pour l'envoi d'emails
 * Facilite la réutilisation et la maintenance du code d'envoi d'emails
 */
class EmailService
{
    public function __construct(
        private MailerInterface $mailer,
        private LoggerInterface $logger,
        private string $fromEmail,
        private string $fromName,
        private string $adminEmail
    ) {
    }

    /**
     * Envoie un email de notification pour un nouveau message de contact
     * 
     * @param object $message L'entité Message contenant les données du contact
     * @param string|null $recipientEmail Email du destinataire (optionnel, sinon utilise l'email admin par défaut)
     * @return bool True si l'email a été envoyé avec succès, false sinon
     */
    public function sendContactNotification(object $message, ?string $recipientEmail = null): bool
    {
        $recipient = $recipientEmail ?? $this->adminEmail;

        $email = (new TemplatedEmail())
            ->from(new Address($this->fromEmail, $this->fromName))
            ->to($recipient)
            ->subject('Nouveau message de contact - ' . $message->getSubject())
            ->htmlTemplate('emails/contact_notification.html.twig')
            ->context([
                'message' => $message,
            ]);

        return $this->sendEmail($email);
    }

    /**
     * Envoie un email de confirmation à l'expéditeur du message de contact
     * 
     * @param object $message L'entité Message contenant les données du contact
     * @return bool True si l'email a été envoyé avec succès, false sinon
     */
    public function sendContactConfirmation(object $message): bool
    {
        $email = (new TemplatedEmail())
            ->from(new Address($this->fromEmail, $this->fromName))
            ->to($message->getEmail())
            ->subject('Confirmation de réception - ' . $message->getSubject())
            ->htmlTemplate('emails/contact_confirmation.html.twig')
            ->context([
                'message' => $message,
            ]);

        return $this->sendEmail($email);
    }

    /**
     * Méthode générique pour envoyer un email templated
     * 
     * @param string $recipient Email du destinataire
     * @param string $subject Sujet de l'email
     * @param string $template Chemin du template Twig
     * @param array $context Variables à passer au template
     * @return bool True si l'email a été envoyé avec succès, false sinon
     */
    public function sendTemplatedEmail(string $recipient, string $subject, string $template, array $context = []): bool
    {
        $email = (new TemplatedEmail())
            ->from(new Address($this->fromEmail, $this->fromName))
            ->to($recipient)
            ->subject($subject)
            ->htmlTemplate($template)
            ->context($context);

        return $this->sendEmail($email);
    }

    /**
     * Méthode privée pour l'envoi effectif de l'email avec gestion des erreurs
     * 
     * @param TemplatedEmail $email L'email à envoyer
     * @return bool True si l'email a été envoyé avec succès, false sinon
     */
    private function sendEmail(TemplatedEmail $email): bool
    {
        try {
            $this->mailer->send($email);
            $this->logger->info('Email envoyé avec succès', [
                'to' => $email->getTo(),
                'subject' => $email->getSubject(),
            ]);
            return true;
        } catch (TransportExceptionInterface $e) {
            $this->logger->error('Erreur lors de l\'envoi d\'email', [
                'to' => $email->getTo(),
                'subject' => $email->getSubject(),
                'error' => $e->getMessage(),
            ]);
            return false;
        } catch (\Exception $e) {
            $this->logger->error('Erreur inattendue lors de l\'envoi d\'email', [
                'to' => $email->getTo(),
                'subject' => $email->getSubject(),
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }
}
