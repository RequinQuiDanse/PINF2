<?php

namespace App\Controller\Admin;

use App\Entity\Webinar;
use App\Form\WebinarType;
use App\Repository\PersonRepository;
use App\Repository\WebinarRepository;
use App\Service\EmailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/webinars')]
class AdminWebinarController extends AbstractController
{
    #[Route('/', name: 'admin_webinar_index')]
    public function index(WebinarRepository $repository, PersonRepository $personRepository): Response
    {
        return $this->render('admin/webinar/index.html.twig', [
            'webinars' => $repository->findBy([], ['date' => 'DESC']),
            'subscribers' => $personRepository->findSubscribedToNewsletter(),
            'allPersons' => $personRepository->findBy([], ['lastName' => 'ASC']),
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

    #[Route('/{id}/send-email', name: 'admin_webinar_send_email', methods: ['POST'])]
    public function sendEmail(
        Request $request,
        Webinar $webinar,
        PersonRepository $personRepository,
        EmailService $emailService
    ): JsonResponse {
        if (!$this->isCsrfTokenValid('send_email'.$webinar->getId(), $request->request->get('_token'))) {
            return new JsonResponse(['success' => false, 'message' => 'Token CSRF invalide'], 403);
        }

        $sendTo = $request->request->get('send_to', 'all');
        $selectedPersons = $request->request->all('persons') ?? [];

        // Déterminer les destinataires
        if ($sendTo === 'all') {
            $recipients = $personRepository->findSubscribedToNewsletter();
        } elseif ($sendTo === 'selected' && !empty($selectedPersons)) {
            $recipients = $personRepository->findBy(['id' => $selectedPersons]);
        } else {
            return new JsonResponse(['success' => false, 'message' => 'Aucun destinataire sélectionné'], 400);
        }

        if (empty($recipients)) {
            return new JsonResponse(['success' => false, 'message' => 'Aucun destinataire trouvé'], 400);
        }

        $successCount = 0;
        $errorCount = 0;

        foreach ($recipients as $person) {
            $sent = $emailService->sendTemplatedEmail(
                $person->getEmail(),
                'Nouveau webinaire : ' . $webinar->getName(),
                'emails/webinar_notification.html.twig',
                [
                    'webinar' => $webinar,
                    'person' => $person,
                ]
            );

            if ($sent) {
                $successCount++;
            } else {
                $errorCount++;
            }
        }

        $message = sprintf('%d email(s) envoyé(s) avec succès', $successCount);
        if ($errorCount > 0) {
            $message .= sprintf(', %d erreur(s)', $errorCount);
        }

        return new JsonResponse([
            'success' => true,
            'message' => $message,
            'successCount' => $successCount,
            'errorCount' => $errorCount,
        ]);
    }
}
