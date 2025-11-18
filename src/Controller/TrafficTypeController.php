<?php

namespace App\Controller;

use App\Entity\TrafficType;
use App\Form\TrafficTypeType;
use App\Repository\TrafficTypeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/traffic/type')]
final class TrafficTypeController extends AbstractController
{
    #[Route(name: 'app_traffic_type_index', methods: ['GET'])]
    public function index(TrafficTypeRepository $trafficTypeRepository): Response
    {
        return $this->render('traffic_type/index.html.twig', [
            'traffic_types' => $trafficTypeRepository->findAllOrdered(),

        ]);
    }

    #[Route('/new', name: 'app_traffic_type_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $trafficType = new TrafficType();
        $form = $this->createForm(TrafficTypeType::class, $trafficType);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($trafficType);
            $entityManager->flush();
            // Nach erfolgreichem Speichern:
            $this->addFlash('success', 'Die Verkehrsart wurde erfolgreich gespeichert.');
            return $this->redirectToRoute('app_traffic_type_index', [], Response::HTTP_SEE_OTHER);
        } elseif ($form->isSubmitted()) {
            // Nach Fehler (z.B. Validierungsfehler):
            $this->addFlash('danger', 'Es ist ein Fehler aufgetreten. Bitte prüfe die Eingaben.');
        }

        return $this->render('traffic_type/new.html.twig', [
            'traffic_type' => $trafficType,
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_traffic_type_show', methods: ['GET'])]
    public function show(TrafficType $trafficType): Response
    {
        return $this->render('traffic_type/show.html.twig', [
            'traffic_type' => $trafficType,
        ]);
    }

    #[Route('/{id<\d+>}/edit', name: 'app_traffic_type_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, TrafficType $trafficType, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(TrafficTypeType::class, $trafficType);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            // Nach erfolgreichem Speichern:
            $this->addFlash('success', 'Die Verkehrsart wurde erfolgreich gespeichert.');
            return $this->redirectToRoute('app_traffic_type_index', [], Response::HTTP_SEE_OTHER);
        } elseif ($form->isSubmitted()) {
            // Nach Fehler (z.B. Validierungsfehler):
            $this->addFlash('danger', 'Es ist ein Fehler aufgetreten. Bitte prüfe die Eingaben.');
        }

        return $this->render('traffic_type/edit.html.twig', [
            'traffic_type' => $trafficType,
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_traffic_type_delete', methods: ['POST'])]
    public function delete(Request $request, TrafficType $trafficType, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$trafficType->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($trafficType);
            $entityManager->flush();
            // Nach erfolgreichem Speichern:
            $this->addFlash('success', 'Die Verkehrsart wurde erfolgreich gespeichert.');
        } else {
            // Nach Fehler (z.B. nicht gefunden, Validierungsfehler):
            $this->addFlash('danger', 'Es ist ein Fehler aufgetreten. Bitte prüfe die Eingaben.');
        }

        return $this->redirectToRoute('app_traffic_type_index', [], Response::HTTP_SEE_OTHER);
    }
    #[Route('/import', name: 'app_traffic_type_import', methods: ['POST'])]
public function import(Request $request, EntityManagerInterface $em): Response
{
    $data = json_decode($request->getContent(), true);
    if (!is_array($data)) {
        // Nach Fehler:
        $this->addFlash('danger', 'Es ist ein Fehler aufgetreten. Bitte prüfe die Eingaben.');
        return $this->json(['error' => 'Ungültiges JSON. Erwartet wird ein Array von Objekten.'], 400);
    }

    $imported = 0;
    $skipped = 0;
    $skippedDetails = [];

    foreach ($data as $index => $entry) {
        $errors = [];

        // Pflichtfeldprüfung
        if (empty($entry['name'])) {
            $errors[] = 'name fehlt';
        }
        // Optional: Prüfung, ob Typ schon existiert
        // $exists = $em->getRepository(TrafficType::class)->findOneBy(['name' => $entry['name']]);
        // if ($exists) { $errors[] = 'Name existiert schon'; }

        if ($errors) {
            $skipped++;
            $skippedDetails[] = [
                'index' => $index,
                'errors' => $errors,
                'entry' => $entry,
            ];
            continue;
        }

        $type = new TrafficType();
        $type->setName($entry['name']);
        $type->setDescription($entry['description'] ?? null);
        $type->setRegulation($entry['regulation'] ?? null);
        $type->setCategory($entry['category'] ?? null);
        $type->setSpecialNotes($entry['specialNotes'] ?? null);

        $em->persist($type);
        $imported++;
    }

    $em->flush();
    // Nach erfolgreichem Speichern:
    $this->addFlash('success', 'Die Verkehrsarten wurden erfolgreich importiert.');
    return $this->json([
        'imported' => $imported,
        'skipped' => $skipped,
        'skippedDetails' => $skippedDetails,
    ]);
}
#[Route('/sort', name: 'app_traffic_type_sort', methods: ['POST'])]
public function sort(Request $request, TrafficTypeRepository $repo, EntityManagerInterface $em): Response
{
    $data = json_decode($request->getContent(), true);
    if (!isset($data['ids']) || !is_array($data['ids'])) {
        // Nach Fehler:
        $this->addFlash('danger', 'Es ist ein Fehler aufgetreten. Bitte prüfe die Eingaben.');
        return $this->json(['status' => 'error', 'error' => 'Ungültige ID-Liste.'], 400);
    }

    foreach ($data['ids'] as $sort => $id) {
        $trafficType = $repo->find($id);
        if ($trafficType) {
            $trafficType->setSortOrder($sort);
        }
    }
    $em->flush();
    // Nach erfolgreichem Speichern:
    $this->addFlash('success', 'Die Sortierung wurde erfolgreich gespeichert.');
    return $this->json(['status' => 'ok']);
}


}
