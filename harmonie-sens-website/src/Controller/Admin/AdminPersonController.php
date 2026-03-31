<?php

namespace App\Controller\Admin;

use App\Entity\Person;
use App\Form\PersonType;
use App\Repository\PersonRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/persons')]
class AdminPersonController extends AbstractController
{
    #[Route('/', name: 'admin_person_index')]
    public function index(Request $request, PersonRepository $repository): Response
    {
        $search = $request->query->get('q', '');
        
        if ($search) {
            $persons = $repository->search($search);
        } else {
            $persons = $repository->findBy([], ['createdAt' => 'DESC']);
        }
        
        return $this->render('admin/person/index.html.twig', [
            'persons' => $persons,
            'search' => $search,
        ]);
    }

    #[Route('/new', name: 'admin_person_new')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $person = new Person();
        $form = $this->createForm(PersonType::class, $person);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($person);
            $em->flush();

            $this->addFlash('success', 'Personne créée avec succès');
            return $this->redirectToRoute('admin_person_index');
        }

        return $this->render('admin/person/form.html.twig', [
            'form' => $form,
            'person' => $person,
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_person_edit')]
    public function edit(Request $request, Person $person, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(PersonType::class, $person);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $this->addFlash('success', 'Personne modifiée avec succès');
            return $this->redirectToRoute('admin_person_index');
        }

        return $this->render('admin/person/form.html.twig', [
            'form' => $form,
            'person' => $person,
        ]);
    }

    #[Route('/{id}/delete', name: 'admin_person_delete', methods: ['POST'])]
    public function delete(Request $request, Person $person, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete'.$person->getId(), $request->request->get('_token'))) {
            $em->remove($person);
            $em->flush();
            $this->addFlash('success', 'Personne supprimée avec succès');
        }

        return $this->redirectToRoute('admin_person_index');
    }
}
