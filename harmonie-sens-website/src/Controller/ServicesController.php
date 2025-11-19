<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/services')]
class ServicesController extends AbstractController
{
    #[Route('/direction-transition', name: 'app_services_direction')]
    public function directionTransition(): Response
    {
        return $this->render('client/services/direction_transition.html.twig');
    }

    #[Route('/diagnostic-audit', name: 'app_services_diagnostic')]
    public function diagnosticAudit(): Response
    {
        return $this->render('client/services/diagnostic_audit.html.twig');
    }

    #[Route('/formations', name: 'app_services_formations')]
    public function formations(): Response
    {
        return $this->render('client/services/formations.html.twig');
    }

    #[Route('/accompagnement', name: 'app_services_accompagnement')]
    public function accompagnement(): Response
    {
        return $this->render('client/services/accompagnement.html.twig');
    }
}
