<?php

namespace App\Controller;

use App\Entity\Vehicle;
use App\Entity\MileageLog;
use App\Form\VehicleType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;

#[Route('/vehicle')]
final class VehicleController extends AbstractController
{
    #[Route(name: 'app_vehicle_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $vehicles = $entityManager
            ->getRepository(Vehicle::class)
            ->findAll();

        return $this->render('vehicle/index.html.twig', [
            'vehicles' => $vehicles,
        ]);
    }

    #[Route('/new', name: 'app_vehicle_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $vehicle = new Vehicle();
        $form = $this->createForm(VehicleType::class, $vehicle);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // --- Dateien aus der Fahrzeug-Dokumenten-Collection behandeln ---
            $vehicleDocuments = $form->get('vehicleDocuments');
            /** @var UploadedFile|null $uploadedFile */
            $uploadDir = $this->getParameter('vehicle_docs_dir');

            foreach ($vehicleDocuments as $index => $docForm) {
                $uploadedFile = $docForm->get('vehicleDocimage')->getData();
                if ($uploadedFile instanceof UploadedFile) {
                    $newFilename = uniqid('vehicle_doc_').'.'.$uploadedFile->guessExtension();

                    try {
                        $uploadedFile->move($uploadDir, $newFilename);
                    } catch (\Exception $e) {
                        $this->addFlash('danger', 'Fehler beim Hochladen der Datei: ' . $e->getMessage());
                        return $this->render('vehicle/new.html.twig', [
                            'vehicle' => $vehicle,
                            'form' => $form->createView(),
                        ]);
                    }

                    // Pfad in das Entity setzen (relativ zum /public)
                    $docForm->getData()->setVehicleDocimage('uploads/vehicle_documents/'.$newFilename);
                }
            }
            // --- Ende Datei-Handling ---

            $entityManager->persist($vehicle);
            $entityManager->flush();

            // --- MileageLog automatisch anlegen! ---
            $mileageLog = new MileageLog();
            $mileageLog->setVehicle($vehicle);
            $mileageLog->setDriver($vehicle->getDriver());
            $mileageLog->setDate(new \DateTime());
            $mileageLog->setStartMile(0);
            $mileageLog->setEndMile(0);
            $mileageLog->setPurpose('Fahrzeug-Inbetriebnahme');
            $mileageLog->setCreatedAt(new \DateTime());
            $mileageLog->setUpdatedAt(new \DateTime());

            $entityManager->persist($mileageLog);
            $entityManager->flush();
            // --- Ende Automatik ---

            return $this->redirectToRoute('app_vehicle_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('vehicle/new.html.twig', [
            'vehicle' => $vehicle,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_vehicle_show', methods: ['GET'])]
    public function show(Vehicle $vehicle): Response
    {
        return $this->render('vehicle/show.html.twig', [
            'vehicle' => $vehicle,
        ]);
    }

    #[Route('/{id<\d+>}/edit', name: 'app_vehicle_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Vehicle $vehicle, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(VehicleType::class, $vehicle);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // --- Dateien aus der Fahrzeug-Dokumenten-Collection behandeln ---
            $vehicleDocuments = $form->get('vehicleDocuments');
            $uploadDir = $this->getParameter('vehicle_docs_dir');

            foreach ($vehicleDocuments as $docForm) {
                $uploadedFile = $docForm->get('vehicleDocimage')->getData();
                if ($uploadedFile instanceof UploadedFile) {
                    $newFilename = uniqid('vehicle_doc_').'.'.$uploadedFile->guessExtension();

                    try {
                        $uploadedFile->move($uploadDir, $newFilename);
                    } catch (\Exception $e) {
                        $this->addFlash('danger', 'Fehler beim Hochladen der Datei: ' . $e->getMessage());
                        return $this->render('vehicle/edit.html.twig', [
                            'vehicle' => $vehicle,
                            'form' => $form->createView(),
                        ]);
                    }

                    // Pfad ins Entity setzen (relativ zum /public)
                    $docForm->getData()->setVehicleDocimage('uploads/vehicle_documents/'.$newFilename);
                }
            }
            // --- Ende Datei-Handling ---

            $entityManager->flush();

            return $this->redirectToRoute('app_vehicle_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('vehicle/edit.html.twig', [
            'vehicle' => $vehicle,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_vehicle_delete', methods: ['POST'])]
    public function delete(Request $request, Vehicle $vehicle, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$vehicle->getId(), $request->request->get('_token'))) {
            $entityManager->remove($vehicle);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_vehicle_index', [], Response::HTTP_SEE_OTHER);
    }
#[Route('/import', name: 'app_vehicle_import', methods: ['POST'])]
public function import(Request $request, EntityManagerInterface $em): Response
{
    $data = json_decode($request->getContent(), true);

    if (json_last_error() !== JSON_ERROR_NONE || !$data || !is_array($data)) {
        return $this->json([
            'error' => 'Invalid or empty JSON.',
            'details' => json_last_error_msg()
        ], Response::HTTP_BAD_REQUEST);
    }

    try {
        /** ========== 1. Fahrzeug ermitteln (ortlogId > licensePlate) ========== **/
        $vehicle = null;
        if (!empty($data['ortlogId'])) {
            $vehicle = $em->getRepository(Vehicle::class)->findOneBy(['ortlogId' => (int)$data['ortlogId']]);
        } elseif (!empty($data['licensePlate'])) {
            $vehicle = $em->getRepository(Vehicle::class)->findOneBy(['licensePlate' => $data['licensePlate']]);
        }

        $isNew = false;
        if (!$vehicle) {
            $vehicle = new Vehicle();
            $isNew = true;
        }

        /** ========== 2. Pflichtfelder prüfen ========== **/
        $requiredFields = ['licensePlate'];
        $missingFields = [];
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                $missingFields[] = $field;
            }
        }

        if ($missingFields) {
            return $this->json([
                'error'   => 'Missing required fields.',
                'missing' => $missingFields
            ], Response::HTTP_BAD_REQUEST);
        }

        /** ========== 3. Sanitizing Kennzeichen ========== **/
        $licensePlate = trim($data['licensePlate']);
        $vehicle->setLicensePlate($licensePlate);

        // licensePlateSanitized automatisch generieren
        $sanitized = strtoupper(preg_replace('/[^A-Z0-9]/i', '', $licensePlate));
        if (method_exists($vehicle, 'setLicensePlateSanitized')) {
            $vehicle->setLicensePlateSanitized($sanitized);
        }

        /** ========== 4. Zuweisung / Aktualisierung ========== **/
        $vehicle->setType($data['type'] ?? $vehicle->getType() ?? 'unbekannt');
        $vehicle->setOrtlogId(
            isset($data['ortlogId']) && $data['ortlogId'] !== '' ? (int)$data['ortlogId'] : $vehicle->getOrtlogId()
        );

        // neu: ortlogTransid übernehmen
        if (isset($data['ortlogTransid']) && $data['ortlogTransid'] !== '') {
            $vehicle->setOrtlogTransid($data['ortlogTransid']);
        }

        // Wenn keine vehicleNumber vorhanden → "unknown"
        $vehicle->setVehicleNumber(!empty($data['vehicleNumber']) ? $data['vehicleNumber'] : 'unknown');

        $vehicle->setBezeichnung($data['bezeichnung'] ?? $vehicle->getBezeichnung());
        $vehicle->setVin($data['vin'] ?? $vehicle->getVin());
        $vehicle->setSeatCount((int)($data['seatCount'] ?? $vehicle->getSeatCount() ?? 1));
        $vehicle->setWheelchair(!empty($data['wheelchair']));
        $vehicle->setLength(isset($data['length']) ? (float)$data['length'] : $vehicle->getLength());
        $vehicle->setWidth(isset($data['width']) ? (float)$data['width'] : $vehicle->getWidth());
        $vehicle->setHeight(isset($data['height']) ? (float)$data['height'] : $vehicle->getHeight());
        $vehicle->setEmptyWeight(isset($data['emptyWeight']) ? (float)$data['emptyWeight'] : $vehicle->getEmptyWeight());
        $vehicle->setMaxWeight(isset($data['maxWeight']) ? (float)$data['maxWeight'] : $vehicle->getMaxWeight());
        $vehicle->setMaxLoad(isset($data['maxLoad']) ? (float)$data['maxLoad'] : $vehicle->getMaxLoad());
        $vehicle->setAxleCount((int)($data['axleCount'] ?? $vehicle->getAxleCount() ?? 2));
        $vehicle->setAxleLoad(isset($data['axleLoad']) ? (float)$data['axleLoad'] : $vehicle->getAxleLoad());

        if (!empty($data['firstRegister'])) {
            $vehicle->setFirstRegister(new \DateTime($data['firstRegister']));
        }
        if (!empty($data['currentRegisterDate'])) {
            $vehicle->setCurrentRegisterDate(new \DateTime($data['currentRegisterDate']));
        }

        $vehicle->setBuildYear((int)($data['buildYear'] ?? $vehicle->getBuildYear()));

        // Status-Logik
        if (!empty($data['ortlogId'])) {
            $vehicle->setStatus($data['status'] ?? $vehicle->getStatus() ?? 'active');
        } else {
            $vehicle->setStatus($data['status'] ?? 'inactive');
        }

        $vehicle->setRegistrationStatus($data['registrationStatus'] ?? $vehicle->getRegistrationStatus() ?? 'zugelassen');
        $vehicle->setToiletStatus($data['toiletStatus'] ?? $vehicle->getToiletStatus());
        $vehicle->setSpeed100($data['speed100'] ?? $vehicle->getSpeed100());
        $vehicle->setAhk(!empty($data['ahk']));
        $vehicle->setTrailerLoad(isset($data['trailerLoad']) ? (float)$data['trailerLoad'] : $vehicle->getTrailerLoad());
        $vehicle->setWheelchairRamp(!empty($data['wheelchairRamp']));
        $vehicle->setLifter(!empty($data['lifter']));
        $vehicle->setStandingCapacity((int)($data['standingCapacity'] ?? $vehicle->getStandingCapacity() ?? 0));
        $vehicle->setRadiotransceiver(!empty($data['radiotransceiver']));

        if (class_exists(\App\Service\SanitizerService::class)) {
            $sanitizer = new \App\Service\SanitizerService();
            $vehicle->updateSanitizedFields($sanitizer);
        }

        /** ========== 5. Speichern ========== **/
        if ($isNew) {
            $em->persist($vehicle);
        }
        $em->flush();

        /** ========== 6. MileageLog bei Neuanlage ========== **/
        if ($isNew) {
            $mileageLog = new MileageLog();
            $mileageLog->setVehicle($vehicle);
            $mileageLog->setDriver($vehicle->getDriver());
            $mileageLog->setDate(new \DateTime());
            $mileageLog->setStartMile(0);
            $mileageLog->setEndMile(0);
            $mileageLog->setPurpose('Import aus API / Excel');
            $mileageLog->setCreatedAt(new \DateTime());
            $mileageLog->setUpdatedAt(new \DateTime());

            $em->persist($mileageLog);
            $em->flush();
        }

        // Doctrine Cache leeren (IdentityMap zurücksetzen)
        $em->clear();

        return $this->json([
            'success'   => true,
            'mode'      => $isNew ? 'created' : 'updated',
            'vehicleId' => $vehicle->getId(),
            'licensePlate' => $vehicle->getLicensePlate(),
            'licensePlateSanitized' => $vehicle->getLicensePlateSanitized() ?? null,
            'ortlogId' => $vehicle->getOrtlogId(),
            'ortlogTransid' => method_exists($vehicle, 'getOrtlogTransid') ? $vehicle->getOrtlogTransid() : null
        ], $isNew ? Response::HTTP_CREATED : Response::HTTP_OK);
    } catch (\Throwable $e) {
        return $this->json([
            'error'   => 'Fehler beim Import.',
            'message' => $e->getMessage(),
            'trace'   => $_ENV['APP_ENV'] === 'dev' ? $e->getTraceAsString() : null
        ], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
}





/**
 * Setzt die ortlogId für ein Fahrzeug anhand des Kennzeichens.
 * POST /vehicle/update-ortlogid
 * 
 * JSON: { "licensePlate": "1 / WES TT 219", "ortlogId": 4092 }
 */
#[Route('/update-ortlogid', name: 'app_vehicle_update_ortlogid', methods: ['POST'])]
public function updateOrtlogId(Request $request, EntityManagerInterface $em): Response
{
    $data = json_decode($request->getContent(), true);

    if (empty($data['licensePlateSanitized']) || empty($data['ortlogId'])) {
        return new JsonResponse([
            'success' => false,
            'error' => 'licensePlateSanitized und ortlogId müssen angegeben werden.'
        ], Response::HTTP_BAD_REQUEST);
    }

    // Suche jetzt nach licensePlateSanitized!
    $vehicle = $em->getRepository(Vehicle::class)
        ->findOneBy(['licensePlateSanitized' => $data['licensePlateSanitized']]);

    if (!$vehicle) {
        return new JsonResponse([
            'success' => false,
            'error' => 'Kein Fahrzeug mit diesem sanitized-Kennzeichen gefunden.'
        ], Response::HTTP_NOT_FOUND);
    }

    // Update
    $vehicle->setOrtlogId($data['ortlogId']);
    // ... nach $vehicle->setOrtlogId(...)
        $vehicle->setOrtlogId(
            isset($data['ortlogId']) && $data['ortlogId'] !== '' ? (int)$data['ortlogId'] : $vehicle->getOrtlogId()
        );

        // NEU: ortlogTransid übernehmen
        if (isset($data['ortlogTransid']) && $data['ortlogTransid'] !== '') {
            $vehicle->setOrtlogTransid($data['ortlogTransid']);
        }
    $em->persist($vehicle);
    $em->flush();

    return new JsonResponse([
        'success' => true,
        'message' => 'ortlogId wurde erfolgreich gesetzt.',
        'vehicleId' => $vehicle->getId(),
        'licensePlateSanitized' => $vehicle->getLicensePlateSanitized(),
        'ortlogId' => $vehicle->getOrtlogId()
    ]);
}



}
