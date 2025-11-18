<?php

namespace App\Controller;

use App\Entity\IdeaPool;
use App\Form\IdeaPoolForm;
use App\Repository\IdeaPoolRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/idea-pool')]
final class IdeaPoolController extends AbstractController
{
    #[Route(name: 'app_idea_pool_index', methods: ['GET'])]
    public function index(IdeaPoolRepository $ideaPoolRepository): Response
    {
        return $this->render('idea_pool/index.html.twig', [
            'idea_pools' => $ideaPoolRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_idea_pool_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $ideaPool = new IdeaPool();
        $form = $this->createForm(IdeaPoolForm::class, $ideaPool);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($ideaPool);
            $entityManager->flush();

            return $this->redirectToRoute('app_idea_pool_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('idea_pool/new.html.twig', [
            'idea_pool' => $ideaPool,
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_idea_pool_show', methods: ['GET'])]
    public function show(IdeaPool $ideaPool): Response
    {
        return $this->render('idea_pool/show.html.twig', [
            'idea_pool' => $ideaPool,
        ]);
    }

    #[Route('/{id<\d+>}/edit', name: 'app_idea_pool_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, IdeaPool $ideaPool, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(IdeaPoolForm::class, $ideaPool);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_idea_pool_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('idea_pool/edit.html.twig', [
            'idea_pool' => $ideaPool,
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_idea_pool_delete', methods: ['POST'])]
    public function delete(Request $request, IdeaPool $ideaPool, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$ideaPool->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($ideaPool);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_idea_pool_index', [], Response::HTTP_SEE_OTHER);
    }
}
