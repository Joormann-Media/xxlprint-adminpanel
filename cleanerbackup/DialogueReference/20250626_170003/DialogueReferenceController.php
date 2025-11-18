<?php

namespace App\Controller;

use App\Entity\DialogueReference;
use App\Form\DialogueReferenceForm;
use App\Repository\DialogueReferenceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/dialogue/reference')]
final class DialogueReferenceController extends AbstractController
{
    #[Route(name: 'app_dialogue_reference_index', methods: ['GET'])]
    public function index(DialogueReferenceRepository $dialogueReferenceRepository): Response
    {
        return $this->render('dialogue_reference/index.html.twig', [
            'dialogue_references' => $dialogueReferenceRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_dialogue_reference_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $dialogueReference = new DialogueReference();
        $form = $this->createForm(DialogueReferenceForm::class, $dialogueReference);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($dialogueReference);
            $entityManager->flush();

            return $this->redirectToRoute('app_dialogue_reference_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('dialogue_reference/new.html.twig', [
            'dialogue_reference' => $dialogueReference,
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_dialogue_reference_show', methods: ['GET'])]
    public function show(DialogueReference $dialogueReference): Response
    {
        return $this->render('dialogue_reference/show.html.twig', [
            'dialogue_reference' => $dialogueReference,
        ]);
    }

    #[Route('/{id<\d+>}/edit', name: 'app_dialogue_reference_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, DialogueReference $dialogueReference, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(DialogueReferenceForm::class, $dialogueReference);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_dialogue_reference_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('dialogue_reference/edit.html.twig', [
            'dialogue_reference' => $dialogueReference,
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_dialogue_reference_delete', methods: ['POST'])]
    public function delete(Request $request, DialogueReference $dialogueReference, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$dialogueReference->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($dialogueReference);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_dialogue_reference_index', [], Response::HTTP_SEE_OTHER);
    }
}
