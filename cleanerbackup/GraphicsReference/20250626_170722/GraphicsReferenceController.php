<?php

namespace App\Controller;

use App\Entity\GraphicsReference;
use App\Form\GraphicsReferenceForm;
use App\Repository\GraphicsReferenceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/graphics/reference')]
final class GraphicsReferenceController extends AbstractController
{
    #[Route(name: 'app_graphics_reference_index', methods: ['GET'])]
    public function index(GraphicsReferenceRepository $graphicsReferenceRepository): Response
    {
        return $this->render('graphics_reference/index.html.twig', [
            'graphics_references' => $graphicsReferenceRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_graphics_reference_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $graphicsReference = new GraphicsReference();
        $form = $this->createForm(GraphicsReferenceForm::class, $graphicsReference);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($graphicsReference);
            $entityManager->flush();

            return $this->redirectToRoute('app_graphics_reference_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('graphics_reference/new.html.twig', [
            'graphics_reference' => $graphicsReference,
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_graphics_reference_show', methods: ['GET'])]
    public function show(GraphicsReference $graphicsReference): Response
    {
        return $this->render('graphics_reference/show.html.twig', [
            'graphics_reference' => $graphicsReference,
        ]);
    }

    #[Route('/{id<\d+>}/edit', name: 'app_graphics_reference_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, GraphicsReference $graphicsReference, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(GraphicsReferenceForm::class, $graphicsReference);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_graphics_reference_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('graphics_reference/edit.html.twig', [
            'graphics_reference' => $graphicsReference,
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_graphics_reference_delete', methods: ['POST'])]
    public function delete(Request $request, GraphicsReference $graphicsReference, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$graphicsReference->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($graphicsReference);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_graphics_reference_index', [], Response::HTTP_SEE_OTHER);
    }
}
