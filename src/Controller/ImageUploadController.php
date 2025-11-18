<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Intervention\Image\ImageManager;
use Psr\Log\LoggerInterface;

class ImageUploadController extends AbstractController
{
    #[Route('/admin/upload-image', name: 'upload_image', methods: ['POST'])]
    public function uploadImage(Request $request, LoggerInterface $logger): JsonResponse
    {
        file_put_contents('/tmp/upload_debug.txt', "⏩ Upload Controller gestartet\n", FILE_APPEND);

        $file = $request->files->get('file');

        if (!$file) {
            file_put_contents('/tmp/upload_debug.txt', "⛔ Keine Datei empfangen\n", FILE_APPEND);
            $logger->error('Keine Datei empfangen beim Upload');
            return new JsonResponse(['error' => 'Keine Datei hochgeladen'], 400, ['Content-Type' => 'application/json']);
        }

        file_put_contents('/tmp/upload_debug.txt', "✅ Datei empfangen: " . $file->getClientOriginalName() . " (" . $file->getMimeType() . ")\n", FILE_APPEND);
        $logger->info('Datei empfangen: ' . $file->getClientOriginalName() . ', Mime: ' . $file->getMimeType());

        $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/webp'];

        if (!in_array($file->getMimeType(), $allowedMimeTypes)) {
            file_put_contents('/tmp/upload_debug.txt', "⛔ Ungültiger MimeType: " . $file->getMimeType() . "\n", FILE_APPEND);
            $logger->error('Ungültiger MimeType: ' . $file->getMimeType());
            return new JsonResponse(['error' => 'Nur JPG, PNG und WebP erlaubt.'], 400, ['Content-Type' => 'application/json']);
        }

        try {
            $uploadDir = $this->getParameter('upload_directory');

            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0775, true);
                file_put_contents('/tmp/upload_debug.txt', "📁 Upload-Ordner erstellt: $uploadDir\n", FILE_APPEND);
            } else {
                file_put_contents('/tmp/upload_debug.txt', "📂 Upload-Ordner existiert: $uploadDir\n", FILE_APPEND);
            }

            $filename = uniqid() . '.' . $file->guessExtension();
            $fullPath = $uploadDir . '/' . $filename;

            $file->move($uploadDir, $filename);
            file_put_contents('/tmp/upload_debug.txt', "📝 Datei verschoben: $fullPath\n", FILE_APPEND);

            // Verwende den GD-Treiber für den ImageManager
            $imageManager = ImageManager::gd(); // GD-Treiber verwenden
            $image = $imageManager->read($fullPath);
            file_put_contents('/tmp/upload_debug.txt', "🖼️ Bild geöffnet: $fullPath\n", FILE_APPEND);

            $maxWidth = 1600;
            if ($image->width() > $maxWidth) {
                $image->resize($maxWidth, null, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });
                file_put_contents('/tmp/upload_debug.txt', "📐 Bild resized auf max. Breite $maxWidth px\n", FILE_APPEND);
            } else {
                file_put_contents('/tmp/upload_debug.txt', "ℹ️ Bild kleiner als $maxWidth px, keine Resize nötig\n", FILE_APPEND);
            }

            $image->save($fullPath, 80);
            file_put_contents('/tmp/upload_debug.txt', "💾 Bild gespeichert: $fullPath\n", FILE_APPEND);

            $logger->info('Bild erfolgreich verarbeitet und gespeichert: ' . $filename);

            $baseUrl = $request->getSchemeAndHttpHost();
            $fullImageUrl = $baseUrl . '/uploads/' . $filename;

            file_put_contents('/tmp/upload_debug.txt', "🚀 Erfolgreiche Bild-URL: " . $fullImageUrl . "\n", FILE_APPEND);

            return new JsonResponse(['location' => $fullImageUrl], 200, ['Content-Type' => 'application/json']);
        } catch (\Exception $e) {
            file_put_contents('/tmp/upload_debug.txt', "🔥 Fehler: " . $e->getMessage() . "\n", FILE_APPEND);
            $logger->error('Upload-Fehler: ' . $e->getMessage());
            return new JsonResponse(['error' => 'Upload fehlgeschlagen: ' . $e->getMessage()], 500, ['Content-Type' => 'application/json']);
        }
    }
}
