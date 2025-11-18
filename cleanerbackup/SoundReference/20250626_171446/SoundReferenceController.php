<?php

namespace App\Controller;

use App\Entity\SoundReference;
use App\Form\SoundReferenceForm;
use App\Repository\SoundReferenceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/sound/reference')]
final class SoundReferenceController extends AbstractController
{
    #[Route(name: 'app_sound_reference_index', methods: ['GET'])]
    public function index(SoundReferenceRepository $soundReferenceRepository): Response
    {
        return $this->render('sound_reference/index.html.twig', [
            'sound_references' => $soundReferenceRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_sound_reference_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $soundReference = new SoundReference();
        $form = $this->createForm(SoundReferenceForm::class, $soundReference);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($soundReference);
            $entityManager->flush();

            return $this->redirectToRoute('app_sound_reference_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('sound_reference/new.html.twig', [
            'sound_reference' => $soundReference,
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_sound_reference_show', methods: ['GET'])]
    public function show(SoundReference $soundReference): Response
    {
        return $this->render('sound_reference/show.html.twig', [
            'sound_reference' => $soundReference,
        ]);
    }

    #[Route('/{id<\d+>}/edit', name: 'app_sound_reference_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, SoundReference $soundReference, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(SoundReferenceForm::class, $soundReference);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_sound_reference_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('sound_reference/edit.html.twig', [
            'sound_reference' => $soundReference,
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_sound_reference_delete', methods: ['POST'])]
    public function delete(Request $request, SoundReference $soundReference, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$soundReference->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($soundReference);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_sound_reference_index', [], Response::HTTP_SEE_OTHER);
    }
}
