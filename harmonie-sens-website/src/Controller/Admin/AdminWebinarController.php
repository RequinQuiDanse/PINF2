<?php

namespace App\Controller\Admin;

use App\Entity\Webinar;
use App\Form\WebinarType;
use App\Repository\WebinarRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/webinars')]
class AdminWebinarController extends AbstractController
{
    #[Route('/', name: 'admin_webinar_index')]
    public function index(WebinarRepository $repository): Response
    {
        return $this->render('admin/webinar/index.html.twig', [
            'webinars' => $repository->findBy([], ['date' => 'DESC']),
        ]);
    }

    #[Route('/new', name: 'admin_webinar_new')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $webinar = new Webinar();
        $form = $this->createForm(WebinarType::class, $webinar);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($webinar);
            $em->flush();

            $this->addFlash('success', 'Webinaire créé avec succès');
            return $this->redirectToRoute('admin_webinar_index');
        }

        return $this->render('admin/webinar/form.html.twig', [
            'form' => $form,
            'webinar' => $webinar,
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_webinar_edit')]
    public function edit(Request $request, Webinar $webinar, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(WebinarType::class, $webinar);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $this->addFlash('success', 'Webinaire modifié avec succès');
            return $this->redirectToRoute('admin_webinar_index');
        }

        return $this->render('admin/webinar/form.html.twig', [
            'form' => $form,
            'webinar' => $webinar,
        ]);
    }

    #[Route('/{id}/delete', name: 'admin_webinar_delete', methods: ['POST'])]
    public function delete(Request $request, Webinar $webinar, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete'.$webinar->getId(), $request->request->get('_token'))) {
            $em->remove($webinar);
            $em->flush();
            $this->addFlash('success', 'Webinaire supprimé avec succès');
        }

        return $this->redirectToRoute('admin_webinar_index');
    }
}
