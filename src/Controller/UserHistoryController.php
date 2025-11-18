<?php

namespace App\Controller;

use App\Entity\UserHistory;
use App\Form\UserHistoryType;
use App\Repository\UserHistoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/user-history')]
final class UserHistoryController extends AbstractController
{
    #[Route(name: 'app_user_history_index', methods: ['GET'])]
    public function index(UserHistoryRepository $userHistoryRepository): Response
    {
        return $this->render('user_history/index.html.twig', [
            'user_histories' => $userHistoryRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_user_history_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $userHistory = new UserHistory();
        $form = $this->createForm(UserHistoryType::class, $userHistory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($userHistory);
            $entityManager->flush();

            return $this->redirectToRoute('app_user_history_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user_history/new.html.twig', [
            'user_history' => $userHistory,
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_user_history_show', methods: ['GET'])]
    public function show(UserHistory $userHistory): Response
    {
        return $this->render('user_history/show.html.twig', [
            'user_history' => $userHistory,
        ]);
    }

    #[Route('/{id<\d+>}/edit', name: 'app_user_history_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, UserHistory $userHistory, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(UserHistoryType::class, $userHistory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_user_history_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user_history/edit.html.twig', [
            'user_history' => $userHistory,
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_user_history_delete', methods: ['POST'])]
    public function delete(Request $request, UserHistory $userHistory, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$userHistory->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($userHistory);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_user_history_index', [], Response::HTTP_SEE_OTHER);
    }
}
