<?php

namespace App\Controller;

use App\Entity\NFCScan;
use App\Form\NFCScanType;
use App\Repository\NFCScanRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;


#[Route('/nfc-scan')]
final class NFCScanController extends AbstractController
{
    #[Route(name: 'app_nfc_scan_index', methods: ['GET'])]
    public function index(NFCScanRepository $nFCScanRepository): Response
    {
        $allScans = $nFCScanRepository->findAll();

    $stats = [
        'total' => count($allScans),
        'nfcCards' => count(array_filter($allScans, fn($scan) => $scan->getMediumType() === 'NFC Karten')),
        'nfcStickers' => count(array_filter($allScans, fn($scan) => $scan->getMediumType() === 'NFC Aufkleber')),
    ];

    return $this->render('nfc_scan/index.html.twig', [
        'nfc_scans' => $allScans,
        'stats' => $stats,
    ]);
}

    #[Route('/new', name: 'app_nfc_scan_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $nFCScan = new NFCScan();
        $form = $this->createForm(NFCScanType::class, $nFCScan);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($nFCScan);
            $entityManager->flush();

            return $this->redirectToRoute('app_nfc_scan_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('nfc_scan/new.html.twig', [
            'nfc_scan' => $nFCScan,
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_nfc_scan_show', methods: ['GET'])]
    public function show(NFCScan $nFCScan): Response
    {
        return $this->render('nfc_scan/show.html.twig', [
            'nfc_scan' => $nFCScan,
        ]);
    }

    #[Route('/{id<\d+>}/edit', name: 'app_nfc_scan_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, NFCScan $nFCScan, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(NFCScanType::class, $nFCScan);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_nfc_scan_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('nfc_scan/edit.html.twig', [
            'nfc_scan' => $nFCScan,
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_nfc_scan_delete', methods: ['POST'])]
    public function delete(Request $request, NFCScan $nFCScan, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$nFCScan->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($nFCScan);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_nfc_scan_index', [], Response::HTTP_SEE_OTHER);
    }
    #[Route('/import', name: 'api_nfc_scan', methods: ['POST'])]
public function scan(Request $request, EntityManagerInterface $em, \App\Repository\NFCScanRepository $repo): JsonResponse
{
    $data = json_decode($request->getContent(), true);

    // Mindestanforderung: UID muss da sein
    if (!isset($data['uid'])) {
        return new JsonResponse(['status' => 'error', 'message' => 'No UID'], 400);
    }

    // Doubletten-Check
    $existing = $repo->findOneBy(['uid' => $data['uid']]);
    if ($existing) {
        // Optional: updaten von Feldern, wenn gewünscht
        $em->flush();
        return new JsonResponse([
            'status' => 'duplicate',
            'id' => $existing->getId(),
            'message' => 'UID already exists'
        ]);
    }

    // Neu anlegen, wenn kein Duplikat
    $scan = new NFCScan();
    $scan->setUid($data['uid']);
    $scan->setScannedAt(new \DateTimeImmutable());

    if (isset($data['atr'])) {
        $scan->setAtr($data['atr']);
    }
    if (isset($data['chipType'])) {
        $scan->setChipType($data['chipType']);
    }
    if (isset($data['memory'])) {
        $scan->setMemory($data['memory']);
    }
    if (isset($data['rawInfo'])) {
        $scan->setRawInfo($data['rawInfo']);
    }
    if (isset($data['manufacturer'])) {
        $scan->setManufacturer($data['manufacturer']);
    }
    if (isset($data['features'])) {
        $scan->setFeatures($data['features']);
    }
    if (isset($data['protocols'])) {
        $scan->setProtocols($data['protocols']);
    }
    if (isset($data['lockStatus'])) {
        $lockStatus = $data['lockStatus'];
        // Falls leerer String, als NULL behandeln
        if ($lockStatus === '' || $lockStatus === null) {
            $scan->setLockStatus(null);
        } elseif (is_string($lockStatus)) {
            $decoded = json_decode($lockStatus, true);
            $scan->setLockStatus(is_array($decoded) ? $decoded : [$lockStatus]);
        } elseif (is_array($lockStatus)) {
            $scan->setLockStatus($lockStatus);
        }
    }
    if (isset($data['isWritable'])) {
        $scan->setIsWritable($data['isWritable']);
    }
    if (isset($data['writeEndurance'])) {
        $scan->setWriteEndurance($data['writeEndurance']);
    }
    if (isset($data['writeCounter'])) {
        $scan->setWriteCounter($data['writeCounter']);
    }

    // NEU: Kategorie und Mediumbeschreibung
    if (isset($data['mediumType'])) {
        $scan->setMediumType($data['mediumType']);
    }
    if (isset($data['mediumDescription'])) {
        $scan->setMediumDescription($data['mediumDescription']);
    }

    $em->persist($scan);
    $em->flush();

    return new JsonResponse(['status' => 'ok', 'id' => $scan->getId()]);
}

#[Route('/import/ping', name: 'api_nfc_scan_ping', methods: ['GET'])]
public function ping(Request $request, EntityManagerInterface $em): JsonResponse
{
    $host = gethostname();
    $phpVersion = phpversion();
    $env = $_ENV['APP_ENV'] ?? 'prod';
    $now = (new \DateTimeImmutable())->format('Y-m-d H:i:s');

    // DB-Check (fehler tolerant!)
    $dbStatus = 'unknown';
try {
    $em->getConnection()->executeQuery('SELECT 1');
    $dbStatus = 'connected';
} catch (\Throwable $e) {
    $dbStatus = 'unreachable';
}


    // Optional: ASCII ausgeben? (z. B. ?ascii=1)
    $includeAscii = $request->query->getBoolean('ascii', true);

    $banner = $includeAscii ? <<<'BANNER'
    ____  ____  ______    __    __  ___  ____  __  __
   (  _ \( ___)(  _ \ \  / /   /__\ / __)(  _ \(  \/  )
    ) _ < )__)  )___/\ \/ /   /(__)\ (__  )   / )    ( 
   (____/(____)(_)\_)\__/   (__)(__)\___)(_)\_)(_/\/\_)
     Monkey Island NFC Endpoint - 9000™
BANNER : null;

    return new JsonResponse([
        'status' => 'ok', // bleibt immer 'ok' – DB hat keinen Einfluss mehr
        'message' => 'pong',
        'ascii' => $banner,
        'time' => $now,
        'server' => $host,
        'env' => $env,
        'php' => $phpVersion,
        'api_version' => 'v1.2.3',
        'db_status' => $dbStatus,
        'meta' => [
            'inspiration' => 'I am Guybrush Threepwood and this is my Ping-Handler!',
            'quote' => 'Never pay more than 20 bucks for a computer API!',
            'credits' => 'joormann-media.de - 🧠 powered by Captain Bea™',
        ],
    ]);
}




}
