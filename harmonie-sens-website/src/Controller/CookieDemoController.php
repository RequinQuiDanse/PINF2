<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CookieDemoController extends AbstractController
{
    /**
     * Page de démonstration du système de gestion des cookies
     * Cette route est uniquement pour les tests et peut être supprimée en production
     */
    #[Route('/cookie-demo', name: 'app_cookie_demo')]
    public function demo(): Response
    {
        return $this->render('example/cookie-demo.html.twig');
    }
}
