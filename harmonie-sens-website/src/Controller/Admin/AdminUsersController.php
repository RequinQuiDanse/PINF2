<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/users')]
class AdminUsersController extends AbstractController
{
    #[Route('/', name: 'app_admin_users_list')]
    public function list(): Response
    {
        return $this->render('admin/users/list.html.twig');
    }

    #[Route('/new', name: 'app_admin_users_new')]
    public function new(): Response
    {
        return $this->render('admin/users/form.html.twig');
    }

    #[Route('/{id}/edit', name: 'app_admin_users_edit')]
    public function edit(int $id): Response
    {
        return $this->render('admin/users/form.html.twig');
    }
}
