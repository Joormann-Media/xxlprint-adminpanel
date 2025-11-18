<?php

namespace App\Controller;

use App\Entity\UserLoginHistory;
use App\Form\UserLoginHistoryForm;
use App\Repository\UserLoginHistoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/user/login/history')]
final class UserLoginHistoryController extends AbstractController
{
    #[Route(name: 'app_user_login_history_index', methods: ['GET'])]
    public function index(UserLoginHistoryRepository $userLoginHistoryRepository): Response
    {
        return $this->render('user_login_history/index.html.twig', [
            'user_login_histories' => $userLoginHistoryRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_user_login_history_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $userLoginHistory = new UserLoginHistory();
        $form = $this->createForm(UserLoginHistoryForm::class, $userLoginHistory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($userLoginHistory);
            $entityManager->flush();

            return $this->redirectToRoute('app_user_login_history_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user_login_history/new.html.twig', [
            'user_login_history' => $userLoginHistory,
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_user_login_history_show', methods: ['GET'])]
    public function show(UserLoginHistory $userLoginHistory): Response
    {
        return $this->render('user_login_history/show.html.twig', [
            'user_login_history' => $userLoginHistory,
        ]);
    }

    #[Route('/{id<\d+>}/edit', name: 'app_user_login_history_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, UserLoginHistory $userLoginHistory, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(UserLoginHistoryForm::class, $userLoginHistory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_user_login_history_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user_login_history/edit.html.twig', [
            'user_login_history' => $userLoginHistory,
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_user_login_history_delete', methods: ['POST'])]
    public function delete(Request $request, UserLoginHistory $userLoginHistory, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$userLoginHistory->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($userLoginHistory);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_user_login_history_index', [], Response::HTTP_SEE_OTHER);
    }
}
