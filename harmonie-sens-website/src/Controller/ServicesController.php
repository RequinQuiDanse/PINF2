<?php

namespace App\Controller;

use App\Repository\ServiceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/services')]
class ServicesController extends AbstractController
{
    public function __construct(
        private ServiceRepository $serviceRepository
    ) {}

    #[Route('/direction-transition', name: 'app_services_direction')]
    public function directionTransition(): Response
    {
        $service = $this->serviceRepository->findBySlug('direction-transition');
        return $this->render('client/services/direction_transition.html.twig', [
            'service' => $service,
        ]);
    }

    #[Route('/diagnostic-audit', name: 'app_services_diagnostic')]
    public function diagnosticAudit(): Response
    {
        $service = $this->serviceRepository->findBySlug('diagnostic-audit');
        return $this->render('client/services/diagnostic_audit.html.twig', [
            'service' => $service,
        ]);
    }

    #[Route('/formations', name: 'app_services_formations')]
    public function formations(): Response
    {
        $service = $this->serviceRepository->findBySlug('formations');
        return $this->render('client/services/formations.html.twig', [
            'service' => $service,
        ]);
    }

    #[Route('/accompagnement', name: 'app_services_accompagnement')]
    public function accompagnement(): Response
    {
        $service = $this->serviceRepository->findBySlug('accompagnement');
        return $this->render('client/services/accompagnement.html.twig', [
            'service' => $service,
        ]);
    }
}
