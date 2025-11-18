<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\KernelInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;


class FileUploadService
{
    private string $userBaseDir;
    private Filesystem $filesystem;
    private LoggerInterface $logger;

    public function __construct(string $userDataBasePath, LoggerInterface $logger)
    {
        $this->userBaseDir = $userDataBasePath;
        $this->filesystem = new Filesystem();
        $this->logger = $logger;
    }

    /**
     * Handle a file upload and return the relative path
     *
     * @param UploadedFile $file Die hochgeladene Datei
     * @param string $userDir Das Benutzerverzeichnis (z. B. '87as9dd12ab')
     * @param string $context Zielordner (avatar, public, private...)
     * @param array $allowedTypes Liste erlaubter MIME-Typen
     * @param bool $resize Optional: Bild skalieren
     * @param bool $scanVirus Optional: ClamAV-Prüfung
     * @return string|null Rückgabe: relativer Pfad der gespeicherten Datei oder null bei Fehler
     */
    public function uploadUserFile(
        UploadedFile $file,
        string $userDir,
        string $context = 'public',
        array $allowedTypes = [],
        bool $resize = false,
        bool $scanVirus = false
    ): ?string {
        try {
            $mimeType = $file->getMimeType();
            if (!empty($allowedTypes) && !in_array($mimeType, $allowedTypes)) {
                throw new \Exception('Nicht erlaubter Dateityp: ' . $mimeType);
            }
    
            if ($scanVirus) {
                // ...
            }
    
            // 👉 Zielpfad korrigieren
            $targetDir = rtrim($this->userBaseDir, '/') . '/' . $userDir . '/' . $context;
            if (!$this->filesystem->exists($targetDir)) {
                $this->filesystem->mkdir($targetDir, 0775);
            }
    
            $safeFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $extension = $file->guessExtension();
            $newFilename = $safeFilename . '-' . uniqid() . '.' . $extension;
    
            $file->move($targetDir, $newFilename);
    
            $this->logger->info('Upload erfolgreich', [
                'userDir' => $userDir,
                'filename' => $newFilename,
                'context' => $context,
                'mime' => $mimeType,
            ]);
    
            // Nur Dateiname zurückgeben!
            return $newFilename;
    
        } catch (\Throwable $e) {
            $this->logger->error('Fehler beim Dateiupload', [
                'message' => $e->getMessage(),
            ]);
            return null;
        }
    }
    
}
