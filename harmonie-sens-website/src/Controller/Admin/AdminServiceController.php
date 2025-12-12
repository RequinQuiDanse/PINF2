<?php

namespace App\Controller\Admin;

use App\Entity\Service;
use App\Form\ServiceType;
use App\Repository\ServiceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/services')]
class AdminServiceController extends AbstractController
{
    #[Route('/', name: 'app_admin_service_index', methods: ['GET'])]
    public function index(ServiceRepository $serviceRepository): Response
    {
        return $this->render('admin/service/index.html.twig', [
            'services' => $serviceRepository->findBy([], ['name' => 'ASC']),
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_service_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Service $service, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ServiceType::class, $service);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Le service a été modifié avec succès.');

            return $this->redirectToRoute('app_admin_service_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/service/edit.html.twig', [
            'service' => $service,
            'form' => $form,
        ]);
    }
}
