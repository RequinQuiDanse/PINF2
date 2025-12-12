<?php

namespace App\Controller\Admin;

use App\Entity\Timetable;
use App\Form\TimetableType;
use App\Repository\TimetableRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/timetable')]
class AdminTimetableController extends AbstractController
{
    #[Route('/', name: 'admin_timetable_index')]
    public function index(TimetableRepository $repository): Response
    {
        return $this->render('admin/timetable/index.html.twig', [
            'timetables' => $repository->findAll(),
        ]);
    }

    #[Route('/new', name: 'admin_timetable_new')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $timetable = new Timetable();
        $form = $this->createForm(TimetableType::class, $timetable);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($timetable);
            $em->flush();

            $this->addFlash('success', 'Horaire créé avec succès');
            return $this->redirectToRoute('admin_timetable_index');
        }

        return $this->render('admin/timetable/form.html.twig', [
            'form' => $form,
            'timetable' => $timetable,
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_timetable_edit')]
    public function edit(Request $request, Timetable $timetable, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(TimetableType::class, $timetable);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $this->addFlash('success', 'Horaire modifié avec succès');
            return $this->redirectToRoute('admin_timetable_index');
        }

        return $this->render('admin/timetable/form.html.twig', [
            'form' => $form,
            'timetable' => $timetable,
        ]);
    }

    #[Route('/{id}/delete', name: 'admin_timetable_delete', methods: ['POST'])]
    public function delete(Request $request, Timetable $timetable, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete'.$timetable->getId(), $request->request->get('_token'))) {
            $em->remove($timetable);
            $em->flush();
            $this->addFlash('success', 'Horaire supprimé avec succès');
        }

        return $this->redirectToRoute('admin_timetable_index');
    }
}
