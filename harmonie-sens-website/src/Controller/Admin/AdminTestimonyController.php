<?php

namespace App\Controller\Admin;

use App\Entity\Testimony;
use App\Form\TestimonyType;
use App\Repository\TestimonyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
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
    public function new(Request $request, EntityManagerInterface $em, SluggerInterface $slugger, LoggerInterface $logger): Response
    {
        $testimony = new Testimony();
        $form = $this->createForm(TestimonyType::class, $testimony);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('imageFile')->getData();
            
            if ($imageFile) {
                try {
                    $publicPath = $this->uploadTestimonyImage($imageFile, $slugger);
                    $testimony->setImagePath($publicPath);
                } catch (\Throwable $e) {
                    $logger->error('Testimony image upload failed', [
                        'exception' => $e,
                        'originalName' => $imageFile->getClientOriginalName(),
                    ]);

                    $debug = (bool) $this->getParameter('kernel.debug');
                    $this->addFlash(
                        'error',
                        'Erreur lors de l\'upload de l\'image'.($debug ? ' : '.$e->getMessage() : '')
                    );

                    return $this->render('admin/testimony/form.html.twig', [
                        'form' => $form,
                        'testimony' => $testimony,
                    ]);
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
    public function edit(Request $request, Testimony $testimony, EntityManagerInterface $em, SluggerInterface $slugger, LoggerInterface $logger): Response
    {
        $form = $this->createForm(TestimonyType::class, $testimony);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('imageFile')->getData();
            
            if ($imageFile) {
                try {
                    $publicPath = $this->uploadTestimonyImage($imageFile, $slugger);
                    $testimony->setImagePath($publicPath);
                } catch (\Throwable $e) {
                    $logger->error('Testimony image upload failed', [
                        'exception' => $e,
                        'originalName' => $imageFile->getClientOriginalName(),
                        'testimonyId' => $testimony->getId(),
                    ]);

                    $debug = (bool) $this->getParameter('kernel.debug');
                    $this->addFlash(
                        'error',
                        'Erreur lors de l\'upload de l\'image'.($debug ? ' : '.$e->getMessage() : '')
                    );

                    return $this->render('admin/testimony/form.html.twig', [
                        'form' => $form,
                        'testimony' => $testimony,
                    ]);
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

    private function uploadTestimonyImage(UploadedFile $imageFile, SluggerInterface $slugger): string
    {
        if (!$imageFile->isValid()) {
            throw new FileException($imageFile->getErrorMessage());
        }

        $uploadDir = $this->getParameter('kernel.project_dir').'/public/uploads/testimonies';

        $filesystem = new Filesystem();
        if (!$filesystem->exists($uploadDir)) {
            $filesystem->mkdir($uploadDir, 0775);
        }

        if (!is_dir($uploadDir) || !is_writable($uploadDir)) {
            throw new FileException(sprintf('Upload directory is not writable: %s', $uploadDir));
        }

        $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = (string) $slugger->slug($originalFilename);
        $extension = $imageFile->guessExtension() ?: $imageFile->getClientOriginalExtension() ?: 'bin';

        $newFilename = $safeFilename.'-'.uniqid().'.'.$extension;
        $imageFile->move($uploadDir, $newFilename);

        return '/uploads/testimonies/'.$newFilename;
    }
}
