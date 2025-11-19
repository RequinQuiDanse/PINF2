<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/messages')]
class AdminMessagesController extends AbstractController
{
    #[Route('/', name: 'app_admin_messages_list')]
    public function list(): Response
    {
        return $this->render('admin/messages/list.html.twig');
    }

    #[Route('/{id}', name: 'app_admin_messages_view')]
    public function view(int $id): Response
    {
        return $this->render('admin/messages/view.html.twig');
    }
}
