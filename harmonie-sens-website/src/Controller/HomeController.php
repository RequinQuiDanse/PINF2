<?php

namespace App\Controller;

use App\Repository\TestimonyRepository;
use App\Repository\WebinarRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(TestimonyRepository $testimonyRepo, WebinarRepository $webinarRepo): Response
    {
        return $this->render('client/home/index.html.twig', [
            'testimonies' => $testimonyRepo->findPublishedTestimonies(),
            'upcoming_webinars' => $webinarRepo->findUpcomingWebinars(),
        ]);
    }
}
