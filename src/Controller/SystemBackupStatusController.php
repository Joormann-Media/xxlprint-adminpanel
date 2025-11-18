<?php

namespace App\Controller;

use App\Entity\SystemBackupStatus;
use App\Form\SystemBackupStatusType;
use App\Repository\SystemBackupStatusRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/system/backup/status')]
final class SystemBackupStatusController extends AbstractController
{
    #[Route(name: 'app_system_backup_status_index', methods: ['GET'])]
    public function index(SystemBackupStatusRepository $systemBackupStatusRepository): Response
    {
        return $this->render('system_backup_status/index.html.twig', [
            'system_backup_statuses' => $systemBackupStatusRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_system_backup_status_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $systemBackupStatus = new SystemBackupStatus();
        $form = $this->createForm(SystemBackupStatusType::class, $systemBackupStatus);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($systemBackupStatus);
            $entityManager->flush();

            return $this->redirectToRoute('app_system_backup_status_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('system_backup_status/new.html.twig', [
            'system_backup_status' => $systemBackupStatus,
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_system_backup_status_show', methods: ['GET'])]
    public function show(SystemBackupStatus $systemBackupStatus): Response
    {
        return $this->render('system_backup_status/show.html.twig', [
            'system_backup_status' => $systemBackupStatus,
        ]);
    }

    #[Route('/{id<\d+>}/edit', name: 'app_system_backup_status_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, SystemBackupStatus $systemBackupStatus, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(SystemBackupStatusType::class, $systemBackupStatus);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_system_backup_status_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('system_backup_status/edit.html.twig', [
            'system_backup_status' => $systemBackupStatus,
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_system_backup_status_delete', methods: ['POST'])]
    public function delete(Request $request, SystemBackupStatus $systemBackupStatus, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$systemBackupStatus->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($systemBackupStatus);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_system_backup_status_index', [], Response::HTTP_SEE_OTHER);
    }
}
