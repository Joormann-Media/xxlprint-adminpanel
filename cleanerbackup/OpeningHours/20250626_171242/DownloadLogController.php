<?php

namespace App\Controller;

use App\Entity\DownloadLog;
use App\Form\DownloadLogForm;
use App\Repository\DownloadLogRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/download/log')]
final class DownloadLogController extends AbstractController
{
    #[Route(name: 'app_download_log_index', methods: ['GET'])]
    public function index(DownloadLogRepository $downloadLogRepository): Response
    {
        return $this->render('download_log/index.html.twig', [
            'download_logs' => $downloadLogRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_download_log_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $downloadLog = new DownloadLog();
        $form = $this->createForm(DownloadLogForm::class, $downloadLog);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($downloadLog);
            $entityManager->flush();

            return $this->redirectToRoute('app_download_log_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('download_log/new.html.twig', [
            'download_log' => $downloadLog,
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_download_log_show', methods: ['GET'])]
    public function show(DownloadLog $downloadLog): Response
    {
        return $this->render('download_log/show.html.twig', [
            'download_log' => $downloadLog,
        ]);
    }

    #[Route('/{id<\d+>}/edit', name: 'app_download_log_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, DownloadLog $downloadLog, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(DownloadLogForm::class, $downloadLog);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_download_log_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('download_log/edit.html.twig', [
            'download_log' => $downloadLog,
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_download_log_delete', methods: ['POST'])]
    public function delete(Request $request, DownloadLog $downloadLog, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$downloadLog->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($downloadLog);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_download_log_index', [], Response::HTTP_SEE_OTHER);
    }
}
