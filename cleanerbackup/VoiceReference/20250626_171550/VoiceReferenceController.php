<?php

namespace App\Controller;

use App\Entity\VoiceReference;
use App\Form\VoiceReferenceForm;
use App\Repository\VoiceReferenceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/voice/reference')]
final class VoiceReferenceController extends AbstractController
{
    #[Route(name: 'app_voice_reference_index', methods: ['GET'])]
    public function index(VoiceReferenceRepository $voiceReferenceRepository): Response
    {
        return $this->render('voice_reference/index.html.twig', [
            'voice_references' => $voiceReferenceRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_voice_reference_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $voiceReference = new VoiceReference();
        $form = $this->createForm(VoiceReferenceForm::class, $voiceReference);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($voiceReference);
            $entityManager->flush();

            return $this->redirectToRoute('app_voice_reference_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('voice_reference/new.html.twig', [
            'voice_reference' => $voiceReference,
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_voice_reference_show', methods: ['GET'])]
    public function show(VoiceReference $voiceReference): Response
    {
        return $this->render('voice_reference/show.html.twig', [
            'voice_reference' => $voiceReference,
        ]);
    }

    #[Route('/{id<\d+>}/edit', name: 'app_voice_reference_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, VoiceReference $voiceReference, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(VoiceReferenceForm::class, $voiceReference);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_voice_reference_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('voice_reference/edit.html.twig', [
            'voice_reference' => $voiceReference,
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_voice_reference_delete', methods: ['POST'])]
    public function delete(Request $request, VoiceReference $voiceReference, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$voiceReference->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($voiceReference);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_voice_reference_index', [], Response::HTTP_SEE_OTHER);
    }
}
