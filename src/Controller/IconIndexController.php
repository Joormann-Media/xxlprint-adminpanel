<?php

namespace App\Controller;

use App\Entity\IconIndex;
use App\Form\IconIndexForm;
use App\Repository\IconIndexRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/icon/index')]
final class IconIndexController extends AbstractController
{
    #[Route(name: 'app_icon_index_index', methods: ['GET'])]
    public function index(IconIndexRepository $iconIndexRepository): Response
    {
        return $this->render('icon_index/index.html.twig', [
            'icon_indices' => $iconIndexRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_icon_index_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $iconIndex = new IconIndex();
        $form = $this->createForm(IconIndexForm::class, $iconIndex);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($iconIndex);
            $entityManager->flush();

            return $this->redirectToRoute('app_icon_index_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('icon_index/new.html.twig', [
            'icon_index' => $iconIndex,
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_icon_index_show', methods: ['GET'])]
    public function show(IconIndex $iconIndex): Response
    {
        return $this->render('icon_index/show.html.twig', [
            'icon_index' => $iconIndex,
        ]);
    }

    #[Route('/{id<\d+>}/edit', name: 'app_icon_index_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, IconIndex $iconIndex, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(IconIndexForm::class, $iconIndex);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_icon_index_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('icon_index/edit.html.twig', [
            'icon_index' => $iconIndex,
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_icon_index_delete', methods: ['POST'])]
    public function delete(Request $request, IconIndex $iconIndex, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$iconIndex->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($iconIndex);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_icon_index_index', [], Response::HTTP_SEE_OTHER);
    }
}
