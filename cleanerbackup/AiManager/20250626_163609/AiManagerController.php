<?php

namespace App\Controller;

use App\Entity\AiManager;
use App\Form\AiManagerForm;
use App\Repository\AiManagerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

#[Route('/ai-manager')]
final class AiManagerController extends AbstractController
{
    #[Route(name: 'app_ai_manager_index', methods: ['GET'])]
    public function index(AiManagerRepository $aiManagerRepository): Response
    {
        return $this->render('ai_manager/index.html.twig', [
            'ai_managers' => $aiManagerRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_ai_manager_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $aiManager = new AiManager();
        $form = $this->createForm(AiManagerForm::class, $aiManager);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($aiManager);
            $entityManager->flush();

            return $this->redirectToRoute('app_ai_manager_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('ai_manager/new.html.twig', [
            'ai_manager' => $aiManager,
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_ai_manager_show', methods: ['GET'])]
    public function show(AiManager $aiManager): Response
    {
        return $this->render('ai_manager/show.html.twig', [
            'ai_manager' => $aiManager,
        ]);
    }

    #[Route('/{id<\d+>}/edit', name: 'app_ai_manager_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, AiManager $aiManager, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(AiManagerForm::class, $aiManager);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_ai_manager_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('ai_manager/edit.html.twig', [
            'ai_manager' => $aiManager,
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_ai_manager_delete', methods: ['POST'])]
    public function delete(Request $request, AiManager $aiManager, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$aiManager->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($aiManager);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_ai_manager_index', [], Response::HTTP_SEE_OTHER);
    }
    #[Route('/upload-avatar', name: 'ai_manager_upload_avatar', methods: ['POST'])]
public function uploadAvatar(Request $request): JsonResponse
{
    /** @var UploadedFile|null $file */
    $file = $request->files->get('file');
    if (!$file) {
        return new JsonResponse(['error' => 'Keine Datei erhalten!'], 400);
    }

    $uploadDir = $this->getParameter('kernel.project_dir') . '/public/system-gfx/system-avatars/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $safeName = uniqid('avatar_') . '.' . $file->guessExtension();
    try {
        $file->move($uploadDir, $safeName);
    } catch (FileException $e) {
        return new JsonResponse(['error' => 'Upload fehlgeschlagen: ' . $e->getMessage()], 500);
    }

    return new JsonResponse([
        'success' => true,
        'filename' => $safeName,
        'url' => '/system-gfx/system-avatars/' . $safeName,
    ]);
}
}
