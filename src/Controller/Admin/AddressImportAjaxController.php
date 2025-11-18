<?php // src/Controller/Admin/AddressImportAjaxController.php

namespace App\Controller\Admin;

use App\Service\AddressImportService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AddressImportAjaxController extends AbstractController
{
    #[Route('/admin/address/import-ajax', name: 'admin_address_import_ajax', methods: ['POST'])]
    public function ajaxImport(Request $request, AddressImportService $addressImportService): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $plz = $data['plz'] ?? null;
        $letter = $data['letter'] ?? null; // falls du nach Buchstaben batchst

        if (!$plz) {
            return new JsonResponse(['error' => 'PLZ fehlt!'], 400);
        }

        // Importiere!
        $result = $addressImportService->importByPlz($plz, $letter);

        // $result sollte so aussehen:
        // ['inserted' => 13, 'skipped' => 7]

        return new JsonResponse([
            'inserted' => $result['inserted'] ?? 0,
            'skipped'  => $result['skipped'] ?? 0,
            'error'    => $result['error'] ?? null,
        ]);
    }
}


