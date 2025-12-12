<?php

namespace App\Controller\Admin;

use App\Entity\ServicePricing;
use App\Form\ServicePricingType;
use App\Repository\ServicePricingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/service-pricing')]
class AdminServicePricingController extends AbstractController
{
    #[Route('/', name: 'admin_service_pricing_index')]
    public function index(ServicePricingRepository $repository): Response
    {
        return $this->render('admin/service_pricing/index.html.twig', [
            'services' => $repository->findAll(),
        ]);
    }

    #[Route('/new', name: 'admin_service_pricing_new')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $service = new ServicePricing();
        $form = $this->createForm(ServicePricingType::class, $service);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($service);
            $em->flush();

            $this->addFlash('success', 'Tarif créé avec succès');
            return $this->redirectToRoute('admin_service_pricing_index');
        }

        return $this->render('admin/service_pricing/form.html.twig', [
            'form' => $form,
            'service' => $service,
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_service_pricing_edit')]
    public function edit(Request $request, ServicePricing $service, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(ServicePricingType::class, $service);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $this->addFlash('success', 'Tarif modifié avec succès');
            return $this->redirectToRoute('admin_service_pricing_index');
        }

        return $this->render('admin/service_pricing/form.html.twig', [
            'form' => $form,
            'service' => $service,
        ]);
    }

    #[Route('/{id}/delete', name: 'admin_service_pricing_delete', methods: ['POST'])]
    public function delete(Request $request, ServicePricing $service, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete'.$service->getId(), $request->request->get('_token'))) {
            $em->remove($service);
            $em->flush();
            $this->addFlash('success', 'Tarif supprimé avec succès');
        }

        return $this->redirectToRoute('admin_service_pricing_index');
    }
}
