<?php

namespace App\Controller;

use App\Entity\VrrBusstop;
use App\Form\VrrBusstopType;
use App\Repository\VrrBusstopRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/vrr-busstop')]
final class VrrBusstopController extends AbstractController
{
    #[Route(name: 'app_vrr_busstop_index', methods: ['GET'])]
public function index(Request $request, VrrBusstopRepository $repo): Response
{
    $search = $request->query->get('search', null);
    $page = max(1, (int) $request->query->get('page', 1));
    $limit = 25;

    // Das liefert 'results' und 'total'
    $data = $repo->searchWithPagination($search, $page, $limit);

    $maxPages = (int) ceil($data['total'] / $limit);

    return $this->render('vrr_busstop/index.html.twig', [
        'vrr_busstops' => $data['results'],
        'currentPage' => $page,
        'maxPages' => $maxPages,
        'searchTerm' => $search,
        'total' => $data['total'],
        'page_title' => 'VRR Busstop Übersicht',
    ]);
}


    #[Route('/new', name: 'app_vrr_busstop_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $vrrBusstop = new VrrBusstop();
        $form = $this->createForm(VrrBusstopType::class, $vrrBusstop);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($vrrBusstop);
            $entityManager->flush();

            return $this->redirectToRoute('app_vrr_busstop_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('vrr_busstop/new.html.twig', [
            'vrr_busstop' => $vrrBusstop,
            'form' => $form,
        ]);
    }

    #[Route('/import', name: 'app_vrr_busstop_import', methods: ['GET', 'POST'])]
    public function import(Request $request, EntityManagerInterface $entityManager, VrrBusstopRepository $repo): Response
    {
        if ($request->isMethod('POST')) {
            // CSRF-Token prüfen
            if (!$this->isCsrfTokenValid('vrr_busstop_import', $request->request->get('_csrf_token'))) {
                $this->addFlash('error', 'Ungültiger CSRF-Token.');
                return $this->redirectToRoute('app_vrr_busstop_import');
            }

            $uploadedFile = $request->files->get('csv_file');

            if (!$uploadedFile) {
                $this->addFlash('error', 'Keine Datei hochgeladen.');
                return $this->redirectToRoute('app_vrr_busstop_import');
            }

            if (!$uploadedFile->isValid()) {
                $this->addFlash('error', 'Datei-Upload fehlgeschlagen. Fehlercode: ' . $uploadedFile->getError());
                return $this->redirectToRoute('app_vrr_busstop_import');
            }

            $ext = strtolower($uploadedFile->getClientOriginalExtension());
            if ($ext !== 'csv') {
                $this->addFlash('error', 'Bitte eine CSV-Datei hochladen.');
                return $this->redirectToRoute('app_vrr_busstop_import');
            }

            $path = $uploadedFile->getRealPath();
            if (!$path || !is_file($path)) {
                $this->addFlash('error', 'Datei konnte nicht gelesen werden.');
                return $this->redirectToRoute('app_vrr_busstop_import');
            }

            try {
                $handle = fopen($path, 'r');
                if (!$handle) {
                    $this->addFlash('error', 'Datei konnte nicht geöffnet werden.');
                    return $this->redirectToRoute('app_vrr_busstop_import');
                }

                // Semikolon als Trennzeichen
                $header = fgetcsv($handle, 0, ';');
                if (!$header) {
                    fclose($handle);
                    $this->addFlash('error', 'CSV-Datei ist leer oder ungültig.');
                    return $this->redirectToRoute('app_vrr_busstop_import');
                }

                $indexes = array_flip($header);

                // Prüfe erforderliche Spalten
                $required = ['STOP_NR', 'VERSION', 'STOP_TYPE', 'STOP_NAME', 'GLOBAL_ID'];
                foreach ($required as $col) {
                    if (!isset($indexes[$col])) {
                        fclose($handle);
                        $this->addFlash('error', "CSV-Spalte '$col' fehlt.");
                        return $this->redirectToRoute('app_vrr_busstop_import');
                    }
                }

                // Optionales Mapping
                $stopNameWoLocalityKey = null;
                if (isset($indexes['STOP_NAME_WO_LOCALITY'])) {
                    $stopNameWoLocalityKey = 'STOP_NAME_WO_LOCALITY';
                } elseif (isset($indexes['STOP_NAME_WITHOUT_LOCALITY'])) {
                    $stopNameWoLocalityKey = 'STOP_NAME_WITHOUT_LOCALITY';
                }

                $stopShortNameKey = null;
                if (isset($indexes['STOP_SHORT_NAME'])) {
                    $stopShortNameKey = 'STOP_SHORT_NAME';
                } elseif (isset($indexes['STOP_SHORTNAME'])) {
                    $stopShortNameKey = 'STOP_SHORTNAME';
                }

                $fareZoneKeys = [];
                for ($i = 1; $i <= 6; $i++) {
                    $key1 = "FARE_ZONE_{$i}_NR";
                    $key2 = "FARE_ZONE{$i}_NR";
                    if (isset($indexes[$key1])) {
                        $fareZoneKeys[$i] = $key1;
                    } elseif (isset($indexes[$key2])) {
                        $fareZoneKeys[$i] = $key2;
                    } else {
                        $fareZoneKeys[$i] = null;
                    }
                }

                $countImported = 0;

                while (($data = fgetcsv($handle, 0, ';')) !== false) {
                    $stopNr = (int)($data[$indexes['STOP_NR']] ?? 0);
                    if (!$stopNr) {
                        continue;
                    }

                    $existing = $repo->find($stopNr);
                    if ($existing) {
                        continue;
                    }

                    $busstop = new VrrBusstop();
                    $busstop->setStopNr($stopNr);
                    $busstop->setVersion((int)($data[$indexes['VERSION']] ?? 0));
                    $busstop->setStopType(!empty($data[$indexes['STOP_TYPE']]) ? (int)$data[$indexes['STOP_TYPE']] : null);
                    $busstop->setStopName(
                        isset($data[$indexes['STOP_NAME']])
                            ? mb_convert_encoding($data[$indexes['STOP_NAME']], 'UTF-8', 'auto')
                            : ''
                    );
                    $busstop->setStopNameWoLocality(
                        $stopNameWoLocalityKey && isset($data[$indexes[$stopNameWoLocalityKey]])
                            ? mb_convert_encoding($data[$indexes[$stopNameWoLocalityKey]], 'UTF-8', 'auto')
                            : null
                    );
                    $busstop->setStopShortName(
                        $stopShortNameKey && isset($data[$indexes[$stopShortNameKey]])
                            ? mb_convert_encoding($data[$indexes[$stopShortNameKey]], 'UTF-8', 'auto')
                            : null
                    );
                    $busstop->setStopPosX(!empty($data[$indexes['STOP_POS_X']]) ? (float)$data[$indexes['STOP_POS_X']] : null);
                    $busstop->setStopPosY(!empty($data[$indexes['STOP_POS_Y']]) ? (float)$data[$indexes['STOP_POS_Y']] : null);
                    $busstop->setPlace(
                        isset($data[$indexes['PLACE']])
                            ? mb_convert_encoding($data[$indexes['PLACE']], 'UTF-8', 'auto')
                            : null
                    );
                    $busstop->setOcc(!empty($data[$indexes['OCC']]) ? (int)$data[$indexes['OCC']] : null);

                    for ($i = 1; $i <= 6; $i++) {
                        $zoneKey = $fareZoneKeys[$i];
                        $setter = "setFareZone{$i}Nr";
                        $busstop->$setter(
                            ($zoneKey && !empty($data[$indexes[$zoneKey]])) ? (int)$data[$indexes[$zoneKey]] : null
                        );
                    }

                    $busstop->setGlobalId(
                        isset($data[$indexes['GLOBAL_ID']])
                            ? mb_convert_encoding($data[$indexes['GLOBAL_ID']], 'UTF-8', 'auto')
                            : ''
                    );

                    $validFrom = $data[$indexes['VALID_FROM']] ?? null;
                    $validTo = $data[$indexes['VALID_TO']] ?? null;
                    $busstop->setValidFrom($validFrom ? new \DateTime($validFrom) : null);
                    $busstop->setValidTo($validTo ? new \DateTime($validTo) : null);

                    $busstop->setPlaceId(
                        isset($data[$indexes['PLACE_ID']])
                            ? mb_convert_encoding($data[$indexes['PLACE_ID']], 'UTF-8', 'auto')
                            : null
                    );
                    $busstop->setGisMotFlag(!empty($data[$indexes['GIS_MOT_FLAG']]) ? (int)$data[$indexes['GIS_MOT_FLAG']] : null);

                    $busstop->setIsCentralStop(!empty($data[$indexes['IS_CENTRAL_STOP']]) && filter_var($data[$indexes['IS_CENTRAL_STOP']], FILTER_VALIDATE_BOOLEAN));
                    $busstop->setIsResponsibleStop(!empty($data[$indexes['IS_RESPONSIBLE_STOP']]) && filter_var($data[$indexes['IS_RESPONSIBLE_STOP']], FILTER_VALIDATE_BOOLEAN));

                    $busstop->setInterchangeType(!empty($data[$indexes['INTERCHANGE_TYPE']]) ? (int)$data[$indexes['INTERCHANGE_TYPE']] : null);
                    $busstop->setInterchangeQuality(!empty($data[$indexes['INTERCHANGE_QUALITY']]) ? (int)$data[$indexes['INTERCHANGE_QUALITY']] : null);

                    $entityManager->persist($busstop);
                    $countImported++;
                }

                fclose($handle);

                $entityManager->flush();

                $this->addFlash('success', "CSV erfolgreich importiert! $countImported Haltestellen wurden hinzugefügt.");
                return $this->redirectToRoute('app_vrr_busstop_index');
            } catch (\Throwable $e) {
                $this->addFlash('error', 'Fehler beim Import: ' . $e->getMessage());
                return $this->redirectToRoute('app_vrr_busstop_import');
            }
        }

        return $this->render('vrr_busstop/import.html.twig');
    }

    #[Route('/{STOP_NR}', name: 'app_vrr_busstop_show', methods: ['GET'], requirements: ['STOP_NR' => '\d+'])]
    public function show(VrrBusstop $vrrBusstop): Response
    {
        return $this->render('vrr_busstop/show.html.twig', [
            'vrr_busstop' => $vrrBusstop,
        ]);
    }

    #[Route('/{STOP_NR}/edit', name: 'app_vrr_busstop_edit', methods: ['GET', 'POST'], requirements: ['STOP_NR' => '\d+'])]
    public function edit(Request $request, VrrBusstop $vrrBusstop, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(VrrBusstopType::class, $vrrBusstop);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_vrr_busstop_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('vrr_busstop/edit.html.twig', [
            'vrr_busstop' => $vrrBusstop,
            'form' => $form,
        ]);
    }

    #[Route('/{STOP_NR}', name: 'app_vrr_busstop_delete', methods: ['POST'], requirements: ['STOP_NR' => '\d+'])]
    public function delete(Request $request, VrrBusstop $vrrBusstop, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$vrrBusstop->getStopNr(), $request->request->get('_token'))) {
            $entityManager->remove($vrrBusstop);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_vrr_busstop_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/api/search', name: 'app_vrr_busstop_api_search', methods: ['GET'])]
    public function apiSearch(Request $request, VrrBusstopRepository $repo): JsonResponse
    {
        $search = $request->query->get('search', null);
        $page = max(1, (int) $request->query->get('page', 1));
        $limit = 25;

        $data = $repo->searchWithPagination($search, $page, $limit);

        // Nur relevante Felder für das Frontend zurückgeben
        $results = array_map(function($busstop) {
            return [
                'stopNr' => $busstop->getStopNr(),
                'stopName' => $busstop->getStopName(),
                'stopNameWoLocality' => $busstop->getStopNameWoLocality(),
                'stopShortName' => $busstop->getStopShortName(),
                'place' => $busstop->getPlace(),
                'globalId' => $busstop->getGlobalId(),
                'placeId' => $busstop->getPlaceId(),
                'gisMotFlag' => $busstop->getGisMotFlag(),
                // ggf. weitere Felder
            ];
        }, $data['results']);

        return new JsonResponse([
            'results' => $results,
            'total' => $data['total'],
        ]);
    }
    // In deinem Controller (z.B. VrrBusstopController)
// src/Controller/VrrBusstopController.php
#[Route('/ajax-list', name: 'app_vrr_busstop_ajax_list', methods: ['GET'])]
public function ajaxList(Request $request, VrrBusstopRepository $repo): JsonResponse
{
    $search = $request->query->get('search', null);
    $page = max(1, (int) $request->query->get('page', 1));
    $limit = 25;

    $data = $repo->searchWithPagination($search, $page, $limit);

    // Tabelle & Pagination HTML erzeugen
    $table = $this->renderView('vrr_busstop/_table.html.twig', [
        'vrr_busstops' => $data['results'],
    ]);
    $paginationHtml = $this->renderView('vrr_busstop/_pagination.html.twig', [
        'maxPages' => (int)ceil($data['total'] / $limit),
        'currentPage' => $page,
        'searchTerm' => $search,
    ]);
    return new JsonResponse([
        'table' => $table,
        'pagination' => $paginationHtml,
    ]);
}


}
