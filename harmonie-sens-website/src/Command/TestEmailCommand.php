<?php

namespace App\Command;

use App\Service\EmailService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:test-email',
    description: 'Test l\'envoi d\'email avec la configuration SMTP',
)]
class TestEmailCommand extends Command
{
    public function __construct(
        private EmailService $emailService,
        private string $adminEmail,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('to', null, InputOption::VALUE_OPTIONAL, 'Email du destinataire (par défaut: admin)')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $recipient = $input->getOption('to') ?? $this->adminEmail;

        $io->title('Test d\'envoi d\'email');
        $io->info('Destinataire: ' . $recipient);

        // Créer un objet de test simulant un message
        $testMessage = new class {
            public function getSubject(): string {
                return 'Test de configuration SMTP';
            }
            public function getEmail(): string {
                return 'test@example.com';
            }
            public function getFirstName(): string {
                return 'Utilisateur';
            }
            public function getLastName(): string {
                return 'Test';
            }
            public function getPhone(): ?string {
                return '+33 1 23 45 67 89';
            }
            public function getMessage(): string {
                return 'Ceci est un message de test pour vérifier la configuration SMTP IONOS.';
            }
            public function getCreatedAt(): \DateTimeInterface {
                return new \DateTime();
            }
        };

        $io->section('Envoi de l\'email de test...');

        try {
            $result = $this->emailService->sendContactNotification($testMessage, $recipient);

            if ($result) {
                $io->success([
                    'Email envoyé avec succès !',
                    'Vérifiez votre boîte de réception: ' . $recipient,
                ]);
                return Command::SUCCESS;
            } else {
                $io->error([
                    'L\'email n\'a pas pu être envoyé.',
                    'Vérifiez les logs pour plus de détails.',
                ]);
                return Command::FAILURE;
            }
        } catch (\Exception $e) {
            $io->error([
                'Erreur lors de l\'envoi:',
                $e->getMessage(),
            ]);
            
            $io->section('Diagnostic de la configuration:');
            $io->listing([
                'Vérifiez MAILER_DSN dans .env.local',
                'Vérifiez que le port 465 n\'est pas bloqué',
                'Vérifiez vos identifiants IONOS',
                'Vérifiez que SSL est bien supporté par votre PHP',
            ]);

            return Command::FAILURE;
        }
    }
}
