<?php

namespace App\Controller;

use App\Entity\PenaltyCatalogEntry;
use App\Form\PenaltyCatalogEntryType;
use App\Repository\PenaltyCatalogEntryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;

#[Route('/penalty-catalog/entry')]
final class PenaltyCatalogEntryController extends AbstractController
{
    #[Route(name: 'app_penalty_catalog_entry_index', methods: ['GET'])]
    public function index(PenaltyCatalogEntryRepository $penaltyCatalogEntryRepository): Response
    {
        return $this->render('penalty_catalog_entry/index.html.twig', [
            'penalty_catalog_entries' => $penaltyCatalogEntryRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_penalty_catalog_entry_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $penaltyCatalogEntry = new PenaltyCatalogEntry();
        $form = $this->createForm(PenaltyCatalogEntryType::class, $penaltyCatalogEntry);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($penaltyCatalogEntry);
            $entityManager->flush();

            return $this->redirectToRoute('app_penalty_catalog_entry_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('penalty_catalog_entry/new.html.twig', [
            'penalty_catalog_entry' => $penaltyCatalogEntry,
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_penalty_catalog_entry_show', methods: ['GET'])]
    public function show(PenaltyCatalogEntry $penaltyCatalogEntry): Response
    {
        return $this->render('penalty_catalog_entry/show.html.twig', [
            'penalty_catalog_entry' => $penaltyCatalogEntry,
        ]);
    }

    #[Route('/{id<\d+>}/edit', name: 'app_penalty_catalog_entry_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, PenaltyCatalogEntry $penaltyCatalogEntry, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PenaltyCatalogEntryType::class, $penaltyCatalogEntry);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_penalty_catalog_entry_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('penalty_catalog_entry/edit.html.twig', [
            'penalty_catalog_entry' => $penaltyCatalogEntry,
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_penalty_catalog_entry_delete', methods: ['POST'])]
    public function delete(Request $request, PenaltyCatalogEntry $penaltyCatalogEntry, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$penaltyCatalogEntry->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($penaltyCatalogEntry);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_penalty_catalog_entry_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/import', name: 'app_penalty_catalog_entry_import', methods: ['GET', 'POST'])]
public function import(Request $request, EntityManagerInterface $em): Response
{
    // 🧠 JSON-API-Fall: application/json
    if ($request->getContentTypeFormat() === 'json' && $request->isMethod('POST')) {
        $data = json_decode($request->getContent(), true);

        if (!is_array($data)) {
            return new JsonResponse(['error' => 'Ungültiges JSON.'], 400);
        }

        $imported = 0;
        foreach ($data as $row) {
            $entry = new PenaltyCatalogEntry();
            $entry->setOffenseTitle($row['offenseTitle']);
            $entry->setDescription($row['description']);
            $entry->setParagraph($row['paragraph'] ?? null);
            $entry->setCategory($row['category']);
            $entry->setVehicleTypes($row['vehicleTypes'] ?? []);
            $entry->setPenaltyMin((int)$row['penaltyMin']);
            $entry->setPenaltyMax($row['penaltyMax'] ?? null);
            $entry->setPoints((int)$row['points']);
            $entry->setDrivingBanMonths($row['drivingBanMonths'] ?? null);
            $entry->setIsProbezeitRelevant((bool)$row['isProbezeitRelevant']);
            $entry->setSeverityLevel($row['severityLevel']);
            $entry->setActive((bool)$row['active']);

            $em->persist($entry);
            $imported++;
        }

        $em->flush();
        return new JsonResponse(['status' => 'ok', 'imported' => $imported]);
    }

    // 🧑‍💻 CSV-Fall via Browser
    if ($request->isMethod('POST')) {
        /** @var UploadedFile $file */
        $file = $request->files->get('csv_file');

        if (!$file || $file->getClientOriginalExtension() !== 'csv') {
            $this->addFlash('danger', 'Bitte eine gültige CSV-Datei hochladen.');
            return $this->redirectToRoute('app_penalty_catalog_entry_import');
        }

        $handle = fopen($file->getPathname(), 'r');
        $headers = fgetcsv($handle, 1000, ',');

        $imported = 0;
        while (($data = fgetcsv($handle, 1000, ',')) !== false) {
            $row = array_combine($headers, $data);

            $entry = new PenaltyCatalogEntry();
            $entry->setOffenseTitle($row['offenseTitle']);
            $entry->setDescription($row['description']);
            $entry->setParagraph($row['paragraph'] ?: null);
            $entry->setCategory($row['category']);
            $entry->setVehicleTypes(json_decode($row['vehicleTypes'] ?? '[]', true));
            $entry->setPenaltyMin((int)$row['penaltyMin']);
            $entry->setPenaltyMax($row['penaltyMax'] !== '' ? (int)$row['penaltyMax'] : null);
            $entry->setPoints((int)$row['points']);
            $entry->setDrivingBanMonths($row['drivingBanMonths'] !== '' ? (int)$row['drivingBanMonths'] : null);
            $entry->setIsProbezeitRelevant(filter_var($row['isProbezeitRelevant'], FILTER_VALIDATE_BOOLEAN));
            $entry->setSeverityLevel($row['severityLevel']);
            $entry->setActive(filter_var($row['active'], FILTER_VALIDATE_BOOLEAN));

            $em->persist($entry);
            $imported++;
        }

        fclose($handle);
        $em->flush();

        $this->addFlash('success', "$imported Bußgeld-Einträge importiert.");
        return $this->redirectToRoute('app_penalty_catalog_entry_index');
    }

    // GET: HTML-Form für CSV
    return $this->render('penalty_catalog_entry/import.html.twig');
}

}
