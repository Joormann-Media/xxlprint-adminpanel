<?php

namespace App\Controller;

use App\Entity\AvailableEntity;
use App\Form\AvailableEntityForm;
use App\Repository\AvailableEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

#[Route('/available-entity')]
final class AvailableEntityController extends AbstractController
{
    #[Route(name: 'app_available_entity_index', methods: ['GET'])]
    public function index(AvailableEntityRepository $availableEntityRepository): Response
    {
        return $this->render('available_entity/index.html.twig', [
            'available_entities' => $availableEntityRepository->findAll(),
            'page_title' => 'Available Entities',
            'page_description' => 'Manage the available entities in the system.',
        ]);
    }

    #[Route('/new', name: 'app_available_entity_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $availableEntity = new AvailableEntity();
        $form = $this->createForm(AvailableEntityForm::class, $availableEntity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($availableEntity);
            $entityManager->flush();

            return $this->redirectToRoute('app_available_entity_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('available_entity/new.html.twig', [
            'available_entity' => $availableEntity,
            'form' => $form,
            'page_title' => 'Create New Available Entity',  
            'page_description' => 'Fill out the form to create a new available entity.',
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_available_entity_show', methods: ['GET'])]
    public function show(AvailableEntity $availableEntity): Response
    {
        return $this->render('available_entity/show.html.twig', [
            'available_entity' => $availableEntity,
            'page_title' => 'Available Entity Details',
            'page_description' => 'View the details of the selected available entity.',
        ]);
    }

    #[Route('/{id<\d+>}/edit', name: 'app_available_entity_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, AvailableEntity $availableEntity, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(AvailableEntityForm::class, $availableEntity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_available_entity_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('available_entity/edit.html.twig', [
            'available_entity' => $availableEntity,
            'form' => $form,
            'page_title' => 'Edit Available Entity',
            'page_description' => 'Modify the details of the available entity.',
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_available_entity_delete', methods: ['POST'])]
    public function delete(Request $request, AvailableEntity $availableEntity, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$availableEntity->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($availableEntity);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_available_entity_index', [], Response::HTTP_SEE_OTHER);
    }
    #[Route('/import', name: 'app_available_entity_import', methods: ['POST'])]
public function import(
    Request $request,
    AvailableEntityRepository $repo,
    EntityManagerInterface $em
): JsonResponse {
    $data = json_decode($request->getContent(), true);

    if (!is_array($data)) {
        return new JsonResponse(['error' => 'Invalid input'], 400);
    }

    $imported = [];
    $skipped = [];

    foreach ($data as $entry) {
        // Nur minimale Felder nötig – Rest kann mit Defaults
        $displayName = $entry['displayName'] ?? null;
        $className = $entry['className'] ?? null;

        if (!$displayName || !$className) {
            $skipped[] = $entry;
            continue;
        }

        // Prüfen, ob schon vorhanden (z.B. anhand className)
        $exists = $repo->findOneBy(['className' => $className]);
        if ($exists) {
            $skipped[] = $entry;
            continue;
        }

        $ae = new AvailableEntity();
        $ae->setDisplayName($displayName);
        $ae->setClassName($className);
        $ae->setTag($entry['tag'] ?? null);
        $ae->setDescription($entry['description'] ?? null);
        $ae->setActive($entry['active'] ?? true);
        $ae->setIcon($entry['icon'] ?? null);
        $ae->setSortOrder($entry['sortOrder'] ?? null);
        $ae->setDependencies($entry['dependencies'] ?? []);
        $ae->setExtraMeta($entry['extraMeta'] ?? []);

        $em->persist($ae);
        $imported[] = $className;
    }
    $em->flush();

    return new JsonResponse([
        'status' => 'ok',
        'imported' => $imported,
        'skipped' => $skipped,
        'import_count' => count($imported),
        'skipped_count' => count($skipped),
    ]);
}
}
