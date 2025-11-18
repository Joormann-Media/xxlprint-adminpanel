<?php

namespace App\Controller;

use App\Entity\SymfonySession;
use App\Form\SymfonySessionForm;
use App\Repository\SymfonySessionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/symfony/session')]
final class SymfonySessionController extends AbstractController
{
    #[Route(name: 'app_symfony_session_index', methods: ['GET'])]
    public function index(SymfonySessionRepository $symfonySessionRepository): Response
    {
        return $this->render('symfony_session/index.html.twig', [
            'symfony_sessions' => $symfonySessionRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_symfony_session_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $symfonySession = new SymfonySession();
        $form = $this->createForm(SymfonySessionForm::class, $symfonySession);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($symfonySession);
            $entityManager->flush();

            return $this->redirectToRoute('app_symfony_session_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('symfony_session/new.html.twig', [
            'symfony_session' => $symfonySession,
            'form' => $form,
        ]);
    }

    #[Route('/{sessId}', name: 'app_symfony_session_show', methods: ['GET'])]
    public function show(SymfonySession $symfonySession): Response
    {
        return $this->render('symfony_session/show.html.twig', [
            'symfony_session' => $symfonySession,
        ]);
    }

    #[Route('/{sessId}/edit', name: 'app_symfony_session_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, SymfonySession $symfonySession, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(SymfonySessionForm::class, $symfonySession);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_symfony_session_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('symfony_session/edit.html.twig', [
            'symfony_session' => $symfonySession,
            'form' => $form,
        ]);
    }

    #[Route('/{sessId}', name: 'app_symfony_session_delete', methods: ['POST'])]
    public function delete(Request $request, SymfonySession $symfonySession, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$symfonySession->getSessId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($symfonySession);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_symfony_session_index', [], Response::HTTP_SEE_OTHER);
    }
}
