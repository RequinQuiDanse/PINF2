<?php

namespace App\Controller\Admin;

use App\Entity\Message;
use App\Repository\MessageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/messages')]
class AdminMessagesController extends AbstractController
{
    #[Route('/', name: 'admin_message_index')]
    public function list(MessageRepository $repository): Response
    {
        $messages = $repository->createQueryBuilder('m')
            ->where('m.isArchived = :archived')
            ->setParameter('archived', false)
            ->orderBy('m.createdAt', 'DESC')
            ->getQuery()
            ->getResult();

        return $this->render('admin/messages/list.html.twig', [
            'messages' => $messages,
        ]);
    }

    #[Route('/{id}', name: 'admin_message_view')]
    public function view(Message $message, EntityManagerInterface $em): Response
    {
        if (!$message->isRead()) {
            $message->setIsRead(true);
            $em->flush();
        }

        return $this->render('admin/messages/view.html.twig', [
            'message' => $message,
        ]);
    }

    #[Route('/{id}/archive', name: 'admin_message_archive', methods: ['POST'])]
    public function archive(Request $request, Message $message, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('archive'.$message->getId(), $request->request->get('_token'))) {
            $message->setIsArchived(true);
            $em->flush();
            $this->addFlash('success', 'Message archivé avec succès');
        }

        return $this->redirectToRoute('admin_message_index');
    }

    #[Route('/{id}/delete', name: 'admin_message_delete', methods: ['POST'])]
    public function delete(Request $request, Message $message, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete'.$message->getId(), $request->request->get('_token'))) {
            $em->remove($message);
            $em->flush();
            $this->addFlash('success', 'Message supprimé avec succès');
        }

        return $this->redirectToRoute('admin_message_index');
    }
}
