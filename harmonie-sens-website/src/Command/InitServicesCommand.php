<?php

namespace App\Command;

use App\Entity\Service;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:init-services',
    description: 'Initialize default services with pricing',
)]
class InitServicesCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $services = [
            [
                'name' => 'Direction de Transition',
                'slug' => 'direction-transition',
                'description' => 'Accompagnement stratégique et opérationnel en direction de transition',
                'priceMin' => 2500,
                'priceMax' => 8000,
                'pricingUnit' => 'mission',
                'pricingDetails' => "• Mission d'audit initial : 2 500 € TTC\n• Accompagnement mensuel : 3 000 € à 5 000 € TTC\n• Mission complète (3-6 mois) : 8 000 € TTC",
            ],
            [
                'name' => 'Diagnostic et Audit',
                'slug' => 'diagnostic-audit',
                'description' => 'Diagnostic organisationnel et audit des pratiques RH',
                'priceMin' => 1500,
                'priceMax' => 4200,
                'pricingUnit' => 'diagnostic',
                'pricingDetails' => "• Audit flash (2-3 jours) : 1 500 € TTC\n• Diagnostic global : 1 800 € à 4 200 € TTC selon profondeur\n• Suivi post-diagnostic : sur devis",
            ],
            [
                'name' => 'Formations',
                'slug' => 'formations',
                'description' => 'Formations sur mesure en management et ressources humaines',
                'priceMin' => 800,
                'priceMax' => 1500,
                'pricingUnit' => 'journée',
                'pricingDetails' => "• Formation intra-entreprise (1 jour) : 800 € à 1 200 € TTC\n• Formation inter-entreprises (par personne) : 350 € TTC\n• Cycle de formations (3-5 jours) : sur devis personnalisé",
            ],
            [
                'name' => 'Accompagnement Individuel',
                'slug' => 'accompagnement',
                'description' => 'Coaching et accompagnement personnalisé',
                'priceMin' => 100,
                'priceMax' => 150,
                'pricingUnit' => 'séance',
                'pricingDetails' => "• Séance de coaching (1h30) : 100 € à 150 € TTC\n• Forfait 5 séances : 450 € TTC\n• Forfait 10 séances : 850 € TTC\n• Accompagnement longue durée : sur devis",
            ],
        ];

        foreach ($services as $serviceData) {
            $service = new Service();
            $service->setName($serviceData['name']);
            $service->setSlug($serviceData['slug']);
            $service->setDescription($serviceData['description']);
            $service->setPriceMin($serviceData['priceMin']);
            $service->setPriceMax($serviceData['priceMax']);
            $service->setPricingUnit($serviceData['pricingUnit']);
            $service->setPricingDetails($serviceData['pricingDetails']);
            $service->setActive(true);

            $this->entityManager->persist($service);
            $io->success(sprintf('Service "%s" créé', $serviceData['name']));
        }

        $this->entityManager->flush();

        $io->success('Tous les services ont été initialisés avec succès!');

        return Command::SUCCESS;
    }
}
