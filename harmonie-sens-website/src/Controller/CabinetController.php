<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/cabinet')]
class CabinetController extends AbstractController
{
    #[Route('/presentation', name: 'app_cabinet_presentation')]
    public function presentation(): Response
    {
        return $this->render('client/cabinet/presentation.html.twig');
    }

    #[Route('/valeurs', name: 'app_cabinet_valeurs')]
    public function valeurs(): Response
    {
        return $this->render('client/cabinet/valeurs.html.twig');
    }

    #[Route('/methodologie', name: 'app_cabinet_methodologie')]
    public function methodologie(): Response
    {
        return $this->render('client/cabinet/methodologie.html.twig');
    }

    #[Route('/secteurs', name: 'app_cabinet_secteurs')]
    public function secteurs(): Response
    {
        return $this->render('client/cabinet/secteurs.html.twig');
    }
}
