<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/content')]
class AdminContentController extends AbstractController
{
    #[Route('/', name: 'app_admin_content_list')]
    public function list(): Response
    {
        return $this->render('admin/content/list.html.twig');
    }

    #[Route('/new', name: 'app_admin_content_new')]
    public function new(): Response
    {
        return $this->render('admin/content/form.html.twig');
    }

    #[Route('/{id}/edit', name: 'app_admin_content_edit')]
    public function edit(int $id): Response
    {
        return $this->render('admin/content/form.html.twig');
    }
}
