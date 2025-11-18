<?php

namespace App\Controller;

use App\Entity\LogHistory;
use App\Form\LogHistoryForm;
use App\Repository\LogHistoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/log/history')]
final class LogHistoryController extends AbstractController
{
    #[Route(name: 'app_log_history_index', methods: ['GET'])]
    public function index(LogHistoryRepository $logHistoryRepository): Response
    {
        return $this->render('log_history/index.html.twig', [
            'log_histories' => $logHistoryRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_log_history_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $logHistory = new LogHistory();
        $form = $this->createForm(LogHistoryForm::class, $logHistory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($logHistory);
            $entityManager->flush();

            return $this->redirectToRoute('app_log_history_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('log_history/new.html.twig', [
            'log_history' => $logHistory,
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_log_history_show', methods: ['GET'])]
    public function show(LogHistory $logHistory): Response
    {
        return $this->render('log_history/show.html.twig', [
            'log_history' => $logHistory,
        ]);
    }

    #[Route('/{id<\d+>}/edit', name: 'app_log_history_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, LogHistory $logHistory, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(LogHistoryForm::class, $logHistory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_log_history_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('log_history/edit.html.twig', [
            'log_history' => $logHistory,
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_log_history_delete', methods: ['POST'])]
    public function delete(Request $request, LogHistory $logHistory, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$logHistory->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($logHistory);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_log_history_index', [], Response::HTTP_SEE_OTHER);
    }
}
