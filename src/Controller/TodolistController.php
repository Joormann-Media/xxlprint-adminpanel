<?php

namespace App\Controller;

use App\Entity\Todolist;
use App\Form\TodolistForm;
use App\Repository\TodolistRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/todolist')]
final class TodolistController extends AbstractController
{
    #[Route(name: 'app_todolist_index', methods: ['GET'])]
    public function index(TodolistRepository $todolistRepository): Response
    {
        return $this->render('todolist/index.html.twig', [
            'todolists' => $todolistRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_todolist_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $todolist = new Todolist();
        $form = $this->createForm(TodolistForm::class, $todolist);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($todolist);
            $entityManager->flush();

            return $this->redirectToRoute('app_todolist_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('todolist/new.html.twig', [
            'todolist' => $todolist,
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_todolist_show', methods: ['GET'])]
    public function show(Todolist $todolist): Response
    {
        return $this->render('todolist/show.html.twig', [
            'todolist' => $todolist,
        ]);
    }

    #[Route('/{id<\d+>}/edit', name: 'app_todolist_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Todolist $todolist, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(TodolistForm::class, $todolist);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_todolist_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('todolist/edit.html.twig', [
            'todolist' => $todolist,
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_todolist_delete', methods: ['POST'])]
    public function delete(Request $request, Todolist $todolist, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$todolist->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($todolist);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_todolist_index', [], Response::HTTP_SEE_OTHER);
    }
}
