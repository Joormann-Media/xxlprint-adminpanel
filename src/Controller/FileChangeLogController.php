<?php

namespace App\Controller;

use App\Entity\FileChangeLog;
use App\Form\FileChangeLogType;
use App\Repository\FileChangeLogRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/file/change/log')]
final class FileChangeLogController extends AbstractController
{
    #[Route(name: 'app_file_change_log_index', methods: ['GET'])]
    public function index(FileChangeLogRepository $fileChangeLogRepository): Response
    {
        return $this->render('file_change_log/index.html.twig', [
            'file_change_logs' => $fileChangeLogRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_file_change_log_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $fileChangeLog = new FileChangeLog();
        $form = $this->createForm(FileChangeLogType::class, $fileChangeLog);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($fileChangeLog);
            $entityManager->flush();

            return $this->redirectToRoute('app_file_change_log_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('file_change_log/new.html.twig', [
            'file_change_log' => $fileChangeLog,
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_file_change_log_show', methods: ['GET'])]
    public function show(FileChangeLog $fileChangeLog): Response
    {
        return $this->render('file_change_log/show.html.twig', [
            'file_change_log' => $fileChangeLog,
        ]);
    }

    #[Route('/{id<\d+>}/edit', name: 'app_file_change_log_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, FileChangeLog $fileChangeLog, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(FileChangeLogType::class, $fileChangeLog);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_file_change_log_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('file_change_log/edit.html.twig', [
            'file_change_log' => $fileChangeLog,
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_file_change_log_delete', methods: ['POST'])]
    public function delete(Request $request, FileChangeLog $fileChangeLog, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$fileChangeLog->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($fileChangeLog);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_file_change_log_index', [], Response::HTTP_SEE_OTHER);
    }
}
