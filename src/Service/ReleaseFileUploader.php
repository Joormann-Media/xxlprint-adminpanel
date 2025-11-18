<?php

namespace App\Service;

use App\Entity\Release;
use App\Entity\ReleaseFile;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;

class ReleaseFileUploader
{
    private Filesystem $filesystem;

    public function __construct(
        private readonly string $targetDirectory, // z.B. %kernel.project_dir%/var/releases
        private readonly LoggerInterface $logger
    ) {
        $this->filesystem = new Filesystem();
    }

    /**
     * Speichert die Datei auf dem Server und erstellt eine ReleaseFile-Entität.
     *
     * @throws \RuntimeException Bei Fehlern beim Dateiupload.
     */
    public function upload(UploadedFile $file, Release $release): ReleaseFile
    {
        $uuid = Uuid::v4()->toRfc4122();
        $extension = $file->guessExtension() ?: 'bin';
        $storedFilename = $uuid . '.' . $extension;

        $releaseId = $release->getId();

        if (!$releaseId) {
            throw new \RuntimeException('Release-ID fehlt. Der Release muss vor dem Upload gespeichert werden.');
        }

        $targetPath = $this->getReleasePath($release);

        // 🔐 Sicherstellen, dass der Ordner existiert
        try {
            if (!$this->filesystem->exists($targetPath)) {
                $this->filesystem->mkdir($targetPath, 0775);
                $this->logger->info('📁 Release-Verzeichnis erstellt', ['path' => $targetPath]);
            }
        } catch (IOExceptionInterface $e) {
            $this->logger->error('❌ Fehler beim Erstellen des Verzeichnisses', ['error' => $e->getMessage()]);
            throw new \RuntimeException('Upload-Verzeichnis konnte nicht erstellt werden.');
        }

        // 💾 Datei verschieben
        try {
            $file->move($targetPath, $storedFilename);
        } catch (\Exception $e) {
            $this->logger->error('❌ Datei konnte nicht gespeichert werden', ['exception' => $e->getMessage()]);
            throw new \RuntimeException('Datei-Upload fehlgeschlagen: ' . $e->getMessage());
        }

        // 📦 ReleaseFile-Entität füllen
        $fullPath = $targetPath . '/' . $storedFilename;
        $filesize = filesize($fullPath) ?: 0;

        $releaseFile = new ReleaseFile();
        $releaseFile->setRelease($release);
        $releaseFile->setOriginalFilename($file->getClientOriginalName());
        $releaseFile->setStoredFilename($storedFilename);
        $releaseFile->setFilesize($filesize);
        $releaseFile->setSha256(hash_file('sha256', $fullPath));
        $releaseFile->setUploadedAt(new \DateTime());
        $releaseFile->setIsPublic(false); // default, kann später überschrieben werden

        $this->logger->info('✅ Datei erfolgreich hochgeladen', [
            'filename' => $storedFilename,
            'release_id' => $releaseId,
            'sha256' => $releaseFile->getSha256(),
            'size' => $filesize,
        ]);

        return $releaseFile;
    }

    /**
     * Gibt den Dateipfad für das Release zurück.
     */
    public function getReleasePath(Release $release): string
    {
        return rtrim($this->targetDirectory, '/') . '/' . $release->getId();
    }
}
