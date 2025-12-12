<?php

namespace App\Controller\Admin;

use App\Repository\MessageRepository;
use App\Repository\PersonRepository;
use App\Repository\TestimonyRepository;
use App\Repository\WebinarRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin')]
class AdminDashboardController extends AbstractController
{
    #[Route('/', name: 'admin_dashboard')]
    public function index(
        MessageRepository $messageRepository,
        PersonRepository $personRepository,
        TestimonyRepository $testimonyRepository,
        WebinarRepository $webinarRepository
    ): Response
    {
        $unreadMessages = $messageRepository->countUnreadMessages();
        $totalPersons = count($personRepository->findAll());
        $newsletterSubscribers = count($personRepository->findSubscribedToNewsletter());
        $publishedTestimonies = count($testimonyRepository->findPublishedTestimonies());
        $upcomingWebinars = count($webinarRepository->findUpcomingWebinars());
        
        return $this->render('admin/dashboard/index.html.twig', [
            'unread_messages' => $unreadMessages,
            'total_persons' => $totalPersons,
            'newsletter_subscribers' => $newsletterSubscribers,
            'published_testimonies' => $publishedTestimonies,
            'upcoming_webinars' => $upcomingWebinars,
        ]);
    }
}
