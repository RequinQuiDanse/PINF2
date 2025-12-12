<?php

namespace App\Controller\Admin;

use App\Entity\Testimony;
use App\Form\TestimonyType;
use App\Repository\TestimonyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/admin/testimonies')]
class AdminTestimonyController extends AbstractController
{
    #[Route('/', name: 'admin_testimony_index')]
    public function index(TestimonyRepository $repository): Response
    {
        return $this->render('admin/testimony/index.html.twig', [
            'testimonies' => $repository->findAll(),
        ]);
    }

    #[Route('/new', name: 'admin_testimony_new')]
    public function new(Request $request, EntityManagerInterface $em, SluggerInterface $slugger): Response
    {
        $testimony = new Testimony();
        $form = $this->createForm(TestimonyType::class, $testimony);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('imageFile')->getData();
            
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('kernel.project_dir').'/public/uploads/testimonies',
                        $newFilename
                    );
                    $testimony->setImagePath('/uploads/testimonies/'.$newFilename);
                } catch (FileException $e) {
                    $this->addFlash('error', 'Erreur lors de l\'upload de l\'image');
                }
            }

            $em->persist($testimony);
            $em->flush();

            $this->addFlash('success', 'Témoignage créé avec succès');
            return $this->redirectToRoute('admin_testimony_index');
        }

        return $this->render('admin/testimony/form.html.twig', [
            'form' => $form,
            'testimony' => $testimony,
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_testimony_edit')]
    public function edit(Request $request, Testimony $testimony, EntityManagerInterface $em, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(TestimonyType::class, $testimony);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('imageFile')->getData();
            
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('kernel.project_dir').'/public/uploads/testimonies',
                        $newFilename
                    );
                    $testimony->setImagePath('/uploads/testimonies/'.$newFilename);
                } catch (FileException $e) {
                    $this->addFlash('error', 'Erreur lors de l\'upload de l\'image');
                }
            }

            $em->flush();

            $this->addFlash('success', 'Témoignage modifié avec succès');
            return $this->redirectToRoute('admin_testimony_index');
        }

        return $this->render('admin/testimony/form.html.twig', [
            'form' => $form,
            'testimony' => $testimony,
        ]);
    }

    #[Route('/{id}/delete', name: 'admin_testimony_delete', methods: ['POST'])]
    public function delete(Request $request, Testimony $testimony, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete'.$testimony->getId(), $request->request->get('_token'))) {
            $em->remove($testimony);
            $em->flush();
            $this->addFlash('success', 'Témoignage supprimé avec succès');
        }

        return $this->redirectToRoute('admin_testimony_index');
    }
}
