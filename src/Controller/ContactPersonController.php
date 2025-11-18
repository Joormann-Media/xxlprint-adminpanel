<?php

namespace App\Controller;

use App\Entity\ContactPerson;
use App\Form\ContactPersonType;
use App\Repository\ContactPersonRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/contact-person')]
final class ContactPersonController extends AbstractController
{
    #[Route(name: 'app_contact_person_index', methods: ['GET'])]
    public function index(ContactPersonRepository $contactPersonRepository): Response
    {
        return $this->render('contact_person/index.html.twig', [
            'contact_people' => $contactPersonRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_contact_person_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $contactPerson = new ContactPerson();
        $form = $this->createForm(ContactPersonType::class, $contactPerson);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($contactPerson);
            $entityManager->flush();

            return $this->redirectToRoute('app_contact_person_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('contact_person/new.html.twig', [
            'contact_person' => $contactPerson,
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_contact_person_show', methods: ['GET'])]
    public function show(ContactPerson $contactPerson): Response
    {
        return $this->render('contact_person/show.html.twig', [
            'contact_person' => $contactPerson,
        ]);
    }

    #[Route('/{id<\d+>}/edit', name: 'app_contact_person_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ContactPerson $contactPerson, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ContactPersonType::class, $contactPerson);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_contact_person_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('contact_person/edit.html.twig', [
            'contact_person' => $contactPerson,
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_contact_person_delete', methods: ['POST'])]
    public function delete(Request $request, ContactPerson $contactPerson, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$contactPerson->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($contactPerson);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_contact_person_index', [], Response::HTTP_SEE_OTHER);
    }
    #[Route('/quick-create', name: 'app_contact_person_quick_create', methods: ['GET', 'POST'])]
public function quickCreate(Request $request, EntityManagerInterface $em): Response
{
    $contactPerson = new ContactPerson();
    $form = $this->createForm(ContactPersonType::class, $contactPerson, [
        'action' => $this->generateUrl('app_contact_person_quick_create'),
    ]);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $em->persist($contactPerson);
        $em->flush();

        return $this->json([
            'success' => true,
            'id' => $contactPerson->getId(),
            'name' => $contactPerson->getFullName(),
        ]);
    }

    return $this->render('contact_person/_quick_form.html.twig', [
        'form' => $form,
    ]);
}

}
