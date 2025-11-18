<?php

namespace App\Controller;

use App\Entity\Release;
use App\Entity\ReleaseFile;
use App\Entity\DownloadLog;
use App\Form\ReleaseType;
use App\Form\ReleaseFileType;
use App\Repository\ReleaseRepository;
use App\Repository\DownloadTokenRepository;
use App\Service\ReleaseFileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use App\Service\DownloadTokenGenerator;



#[Route('/admin/release')]
final class ReleaseController extends AbstractController
{
    public function __construct(
        private readonly ReleaseFileUploader $uploader, // ✅ für Download und Upload
        private readonly EntityManagerInterface $em, // ✅ für Token-Logging
        private readonly RequestStack $requestStack // ✅ für IP/UserAgent im Token-Download
    ) {}

    #[Route(name: 'app_release_index', methods: ['GET'])]
    public function index(ReleaseRepository $releaseRepository): Response
    {
        return $this->render('release/index.html.twig', [
            'page_title' => 'Release Übersicht',
            'releases' => $releaseRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_release_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $release = new Release();
        $form = $this->createForm(ReleaseType::class, $release);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($release);
            $entityManager->flush();

            return $this->redirectToRoute('app_release_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('release/new.html.twig', [
            'page_title' => 'Neues Release erstellen',
            'release' => $release,
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_release_show', methods: ['GET'])]
public function show(
    Release $release,
    DownloadTokenGenerator $tokenGenerator,
    EntityManagerInterface $em,
    Request $request // 👈 für IP-Ermittlung
): Response {
    $tokens = [];

    foreach ($release->getFiles() as $file) {
        $token = $tokenGenerator->generate($file, 240); // 60 Minuten gültig

        // 🧠 IP setzen (falls Spalte `ip` in der Entity vorhanden ist)
        $token->setIp($request->getClientIp());

        $em->persist($token);

        // ⏰ Ablaufzeit als String für Twig
        $tokens[$file->getId()] = [
            'token' => $token->getToken(),
            'expiresAt' => $token->getExpiresAt()?->format('Y-m-d H:i'),
        ];
    }

    $em->flush();

    return $this->render('release/show.html.twig', [
        'page_title' => 'Release Details',
        'release' => $release,
        'releaseFiles' => $release->getFiles(),
        'tokens' => $tokens,
    ]);
}

    






    

    #[Route('/{id<\d+>}/edit', name: 'app_release_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Release $release, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ReleaseType::class, $release);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_release_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('release/edit.html.twig', [
            'page_title' => 'Release bearbeiten',
            'release' => $release,
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_release_delete', methods: ['POST'])]
    public function delete(Request $request, Release $release, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$release->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($release);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_release_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/download/id/{id<\d+>}', name: 'release_file_download')]
    public function download(ReleaseFile $releaseFile): Response
    {
        $this->denyAccessUnlessGranted('DOWNLOAD', $releaseFile); // ✅ Voter check

        $path = $this->uploader->getReleasePath($releaseFile->getRelease()) . '/' . $releaseFile->getStoredFilename();

        if (!file_exists($path)) {
            throw $this->createNotFoundException('Datei nicht gefunden.');
        }

        return $this->file($path, $releaseFile->getOriginalFilename());
    }

    #[Route('/download/token/{token}', name: 'download_with_token')]
public function downloadWithToken(
    string $token,
    DownloadTokenRepository $tokenRepo
): Response {
    // 🔍 Token-Entität prüfen
    $tokenEntity = $tokenRepo->findOneBy(['token' => $token, 'used' => false]);

    if (!$tokenEntity) {
        throw $this->createNotFoundException('❌ Ungültiger oder bereits verwendeter Download-Token');
    }

    // ⏰ Token abgelaufen?
    $expiresAt = $tokenEntity->getExpiresAt();
    if ($expiresAt instanceof \DateTimeInterface && $expiresAt < new \DateTime()) {
        throw $this->createNotFoundException('⚠️ Download-Token ist abgelaufen');
    }

    // 📁 Datei holen
    $releaseFile = $tokenEntity->getReleaseFile();
    if (!$releaseFile) {
        throw $this->createNotFoundException('❌ Keine Datei für diesen Token vorhanden.');
    }

    // 📦 Pfad zur Datei
    $filePath = $this->uploader->getReleasePath($releaseFile->getRelease()) . '/' . $releaseFile->getStoredFilename();
    if (!file_exists($filePath)) {
        throw $this->createNotFoundException('🚫 Datei fehlt auf dem Server.');
    }

    // ✅ Token als verwendet markieren
    $tokenEntity->setUsed(true);
    $tokenEntity->setUsedAt(new \DateTime()); // Set the used timestamp

    // 🧾 Download loggen
    $log = new DownloadLog();
    $log->setReleaseFile($releaseFile);
    $log->setDownloadedAt((new \DateTime())->format('Y-m-d H:i:s')); // Set the current timestamp as a string

    $request = $this->requestStack->getMainRequest();
    $log->setIp($request?->getClientIp() ?? '127.0.0.1');
    $log->setUserAgent($request?->headers->get('User-Agent') ?? 'n/a');
    $log->setToken($token);
    $log->setUser($this->getUser());
    

    // 🔒 Alles speichern
    $this->em->persist($log);
    $this->em->flush();

    // 🎉 Datei zum Download bereitstellen
    return $this->file($filePath, $releaseFile->getOriginalFilename());
}



    #[Route('/{id<\d+>}/upload', name: 'release_file_upload')]
    public function uploadReleaseFile(
        Request $request,
        Release $release,
        EntityManagerInterface $em,
        ReleaseFileUploader $uploader
    ): Response {
        $form = $this->createForm(ReleaseFileType::class);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile|null $file */
            $file = $form->get('file')->getData();
    
            if (!$file instanceof UploadedFile || !$file->isValid()) {
                $this->addFlash('danger', '❌ Die Datei konnte nicht verarbeitet werden.');
                return $this->redirectToRoute('release_file_upload', ['id' => $release->getId()]);
            }
    
            try {
                // 📦 Upload durchführen
                $releaseFile = $uploader->upload($file, $release);
                $releaseFile->setPlatform($form->get('platform')->getData());
                $releaseFile->setIsPublic($form->get('isPublic')->getData());
    
                // Speichern & ID erzeugen
                $em->persist($releaseFile);
                $em->flush();
    
                // 🔗 Download-Link generieren
                $downloadUrl = $this->generateUrl('release_file_download', [
                    'id' => $releaseFile->getId(),
                ], UrlGeneratorInterface::ABSOLUTE_URL);
    
                // In beide Entitäten schreiben
                $releaseFile->setDownloadUrl($downloadUrl);
                $release->setDownloadUrl($downloadUrl);
    
                // ⚠️ Prüfe die Länge (max 255?) sonst Fehler!
                if (strlen($downloadUrl) > 255) {
                    throw new \RuntimeException('Download-URL ist zu lang für DB-Spalte!');
                }
    
                $em->flush();
    
                // ✔️ Erfolgreich
                $this->addFlash('success', sprintf(
                    '✅ Datei "%s" (%s KB) hochgeladen. SHA256: %s',
                    $file->getClientOriginalName(),
                    round($file->getSize() / 1024, 2),
                    $releaseFile->getSha256()
                ));
    
                // DEBUG:
                $this->addFlash('debug', '✔️ Upload abgeschlossen, leite weiter...');
                return new RedirectResponse($this->generateUrl('app_release_index'));

exit('🎯 Du wurdest erfolgreich redirected!');

            } catch (\Throwable $e) {
                // ❌ Fehlerbehandlung & Analyse
                $this->addFlash('danger', '❌ Fehler beim Upload: ' . $e->getMessage());
                dump($e); // ← Entfernen in Produktion
                return new RedirectResponse($this->generateUrl('app_release_index'));

            }
        }
    
        // Wenn das Formular nicht abgeschickt oder ungültig ist
        return $this->render('admin/release_files/upload.html.twig', [
            'page_title' => 'Datei hochladen',
            'release' => $release,
            'form' => $form->createView(),
        ]);
    }
    
    #[Route('/generate-token/{id<\d+>}', name: 'release_file_generate_token')]
public function generateToken(ReleaseFile $releaseFile, DownloadTokenGenerator $generator, EntityManagerInterface $em): Response
{
    $token = $generator->generate($releaseFile);
    $em->persist($token);
    $em->flush();

    $this->addFlash('success', 'Download-Token generiert: ' . $token->getToken());

    return $this->redirectToRoute('app_release_show', ['id' => $releaseFile->getRelease()->getId()]);
}



}
