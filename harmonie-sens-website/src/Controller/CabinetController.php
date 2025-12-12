<?php

namespace App\Controller;

use App\Repository\TestimonyRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/cabinet')]
class CabinetController extends AbstractController
{
    public function __construct(
        private TestimonyRepository $testimonyRepository
    ) {}

    #[Route('/presentation', name: 'app_cabinet_presentation')]
    public function presentation(): Response
    {
        $testimonies = $this->testimonyRepository->findPublishedTestimonies();
        return $this->render('client/cabinet/presentation.html.twig', [
            'testimonies' => $testimonies,
        ]);
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
