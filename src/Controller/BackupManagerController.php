<?php

namespace App\Controller;

use App\Entity\BackupManager;
use App\Form\BackupManagerType;
use App\Repository\BackupManagerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/backup-manager')]
final class BackupManagerController extends AbstractController
{
    #[Route(name: 'app_backup_manager_index', methods: ['GET'])]
    public function index(BackupManagerRepository $backupManagerRepository): Response
    {
        return $this->render('backup_manager/index.html.twig', [
            'backup_managers' => $backupManagerRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_backup_manager_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $backupManager = new BackupManager();
        $form = $this->createForm(BackupManagerType::class, $backupManager);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($backupManager);
            $entityManager->flush();

            return $this->redirectToRoute('app_backup_manager_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('backup_manager/new.html.twig', [
            'backup_manager' => $backupManager,
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_backup_manager_show', methods: ['GET'])]
    public function show(BackupManager $backupManager): Response
    {
        return $this->render('backup_manager/show.html.twig', [
            'backup_manager' => $backupManager,
        ]);
    }

    #[Route('/{id<\d+>}/edit', name: 'app_backup_manager_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, BackupManager $backupManager, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(BackupManagerType::class, $backupManager);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_backup_manager_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('backup_manager/edit.html.twig', [
            'backup_manager' => $backupManager,
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_backup_manager_delete', methods: ['POST'])]
    public function delete(Request $request, BackupManager $backupManager, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$backupManager->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($backupManager);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_backup_manager_index', [], Response::HTTP_SEE_OTHER);
    }
    #[Route('/import', name: 'app_backup_manager_import', methods: ['POST'])]
public function import(Request $request, EntityManagerInterface $entityManager, SerializerInterface $serializer): JsonResponse
{
    $data = json_decode($request->getContent(), true);

    // Minimal-Validierung:
    if (!$data || empty($data['type'])) {
        return new JsonResponse(['status' => 'error', 'message' => 'type fehlt oder keine Daten übergeben!'], 400);
    }

    $backup = new BackupManager();
    $backup->setType($data['type']);
    $backup->setPathSql($data['pathSql'] ?? null);
    $backup->setPathProject($data['pathProject'] ?? null);
    $backup->setGitRemoteStatus($data['gitRemoteStatus'] ?? null);
    $backup->setGitStatusTimestamp(!empty($data['gitStatusTimestamp']) ? new \DateTime($data['gitStatusTimestamp']) : null);
    $backup->setGitStatusMessage($data['gitStatusMessage'] ?? null);
    $backup->setNotes($data['notes'] ?? null);

    $entityManager->persist($backup);
    $entityManager->flush();

    return new JsonResponse([
        'status' => 'ok',
        'id' => $backup->getId(),
        'createdAt' => $backup->getCreatedAt()?->format('Y-m-d H:i:s'),
    ]);
}
}
