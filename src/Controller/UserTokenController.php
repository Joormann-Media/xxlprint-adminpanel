<?php

namespace App\Controller;

use App\Entity\UserToken;
use App\Form\UserTokenType;
use App\Repository\UserTokenRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/user-token')]
final class UserTokenController extends AbstractController
{
    #[Route(name: 'app_user_token_index', methods: ['GET'])]
    public function index(UserTokenRepository $userTokenRepository): Response
    {
        return $this->render('user_token/index.html.twig', [
            'user_tokens' => $userTokenRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_user_token_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $userToken = new UserToken();
        $form = $this->createForm(UserTokenType::class, $userToken);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($userToken);
            $entityManager->flush();

            return $this->redirectToRoute('app_user_token_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user_token/new.html.twig', [
            'user_token' => $userToken,
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_user_token_show', methods: ['GET'])]
    public function show(UserToken $userToken): Response
    {
        return $this->render('user_token/show.html.twig', [
            'user_token' => $userToken,
        ]);
    }

    #[Route('/{id<\d+>}/edit', name: 'app_user_token_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, UserToken $userToken, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(UserTokenType::class, $userToken);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_user_token_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user_token/edit.html.twig', [
            'user_token' => $userToken,
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_user_token_delete', methods: ['POST'])]
    public function delete(Request $request, UserToken $userToken, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$userToken->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($userToken);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_user_token_index', [], Response::HTTP_SEE_OTHER);
    }
}
