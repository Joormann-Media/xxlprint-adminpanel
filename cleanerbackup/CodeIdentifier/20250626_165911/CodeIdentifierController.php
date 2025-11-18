<?php

namespace App\Controller;

use App\Entity\CodeIdentifier;
use App\Form\CodeIdentifierForm;
use App\Repository\CodeIdentifierRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpFoundation\JsonResponse;

#[Route('/code-identifier')]
final class CodeIdentifierController extends AbstractController
{
    #[Route(name: 'app_code_identifier_index', methods: ['GET'])]
    public function index(CodeIdentifierRepository $codeIdentifierRepository): Response
    {
        return $this->render('code_identifier/index.html.twig', [
            'code_identifiers' => $codeIdentifierRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_code_identifier_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $codeIdentifier = new CodeIdentifier();
        $form = $this->createForm(CodeIdentifierForm::class, $codeIdentifier);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($codeIdentifier);
            $entityManager->flush();

            return $this->redirectToRoute('app_code_identifier_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('code_identifier/new.html.twig', [
            'code_identifier' => $codeIdentifier,
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_code_identifier_show', methods: ['GET'])]
    public function show(CodeIdentifier $codeIdentifier): Response
    {
        return $this->render('code_identifier/show.html.twig', [
            'code_identifier' => $codeIdentifier,
        ]);
    }

    #[Route('/{id<\d+>}/edit', name: 'app_code_identifier_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, CodeIdentifier $codeIdentifier, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CodeIdentifierForm::class, $codeIdentifier);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_code_identifier_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('code_identifier/edit.html.twig', [
            'code_identifier' => $codeIdentifier,
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_code_identifier_delete', methods: ['POST'])]
    public function delete(Request $request, CodeIdentifier $codeIdentifier, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$codeIdentifier->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($codeIdentifier);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_code_identifier_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/import', name: 'app_code_identifier_import', methods: ['POST'])]
public function import(Request $request, EntityManagerInterface $entityManager): JsonResponse
{
    $data = json_decode($request->getContent(), true);

    if (!$data) {
        throw new BadRequestHttpException('Invalid JSON payload');
    }

    // Multi-Import
    if (isset($data[0]) && is_array($data[0])) {
        $ids = [];
        foreach ($data as $entry) {
            $codeIdentifier = new CodeIdentifier();
            $codeIdentifier->setIdentifier($entry['identifier'] ?? null);
            $codeIdentifier->setTarget($entry['target'] ?? null);
            $codeIdentifier->setDescription($entry['description'] ?? null);

            // Optional: Minimal-Validierung
            if (empty($entry['identifier'])) {
                continue; // oder Fehler sammeln
            }

            $entityManager->persist($codeIdentifier);
            $ids[] = $codeIdentifier->getId();
        }
        $entityManager->flush();
        return new JsonResponse(['status' => 'OK', 'ids' => $ids], 201);
    }

    // Single-Import
    if (is_array($data)) {
        $codeIdentifier = new CodeIdentifier();
        $codeIdentifier->setIdentifier($data['identifier'] ?? null);
        $codeIdentifier->setTarget($data['target'] ?? null);
        $codeIdentifier->setDescription($data['description'] ?? null);

        if (empty($data['identifier'])) {
            throw new BadRequestHttpException('Identifier is required');
        }

        $entityManager->persist($codeIdentifier);
        $entityManager->flush();

        return new JsonResponse(['status' => 'OK', 'id' => $codeIdentifier->getId()], 201);
    }

    throw new BadRequestHttpException('Invalid JSON structure');
}
#[Route('/bulk-delete', name: 'app_code_identifier_bulk_delete', methods: ['POST'])]
public function bulkDelete(Request $request, EntityManagerInterface $entityManager, CodeIdentifierRepository $repo): JsonResponse
{
    $data = json_decode($request->getContent(), true);

    if (!is_array($data) || !isset($data['ids']) || !is_array($data['ids'])) {
        return new JsonResponse(['status' => 'error', 'message' => 'Bitte ein JSON-Array mit "ids": [1,2,3] senden!'], 400);
    }

    $ids = array_filter(array_map('intval', $data['ids']));
    $deleted = [];
    $notFound = [];

    foreach ($ids as $id) {
        $entity = $repo->find($id);
        if ($entity) {
            $entityManager->remove($entity);
            $deleted[] = $id;
        } else {
            $notFound[] = $id;
        }
    }

    if (count($deleted)) {
        $entityManager->flush();
    }

    return new JsonResponse([
        'status'    => 'OK',
        'deleted'   => $deleted,
        'not_found' => $notFound,
        'message'   => count($deleted) . ' gelöscht. ' . (count($notFound) ? 'Nicht gefunden: '.implode(', ', $notFound) : 'Alles gelöscht wie bei Murray im Keller.')
    ]);
}

}
