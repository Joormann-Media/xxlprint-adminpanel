<?php

namespace App\Controller;

use App\Entity\UserSession;
use App\Form\UserSessionForm;
use App\Repository\UserSessionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/user/session')]
final class UserSessionController extends AbstractController
{
    #[Route(name: 'app_user_session_index', methods: ['GET'])]
    public function index(UserSessionRepository $userSessionRepository): Response
    {
        return $this->render('user_session/index.html.twig', [
            'user_sessions' => $userSessionRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_user_session_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $userSession = new UserSession();
        $form = $this->createForm(UserSessionForm::class, $userSession);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($userSession);
            $entityManager->flush();

            return $this->redirectToRoute('app_user_session_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user_session/new.html.twig', [
            'user_session' => $userSession,
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_user_session_show', methods: ['GET'])]
    public function show(UserSession $userSession): Response
    {
        return $this->render('user_session/show.html.twig', [
            'user_session' => $userSession,
        ]);
    }

    #[Route('/{id<\d+>}/edit', name: 'app_user_session_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, UserSession $userSession, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(UserSessionForm::class, $userSession);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_user_session_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user_session/edit.html.twig', [
            'user_session' => $userSession,
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_user_session_delete', methods: ['POST'])]
    public function delete(Request $request, UserSession $userSession, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$userSession->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($userSession);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_user_session_index', [], Response::HTTP_SEE_OTHER);
    }
}
