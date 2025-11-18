<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class OcrController extends AbstractController
{
    #[Route('/ocr/fahrzeugschein', name: 'app_ocr_fahrzeugschein')]
    public function upload(Request $request): Response
    {
        $ocrResult = null;
        $error = null;
        $filePreview = null;
        $shellOutput = null;
        $imagePath = null;

        if ($request->isMethod('POST')) {
            /** @var UploadedFile $file */
            $file = $request->files->get('upload_file');
            if ($file && in_array(strtolower($file->getClientOriginalExtension()), ['pdf', 'jpg', 'jpeg', 'png'])) {
                $uploadPath = sys_get_temp_dir() . '/' . uniqid('ocr_') . '.' . $file->getClientOriginalExtension();
                try {
                    $file->move(dirname($uploadPath), basename($uploadPath));
                    $ext = strtolower($file->getClientOriginalExtension());
                    
                    if (in_array($ext, ['jpg', 'jpeg', 'png'])) {
                        // Bildvorschau als Base64
                        $filePreview = 'data:image/' . $ext . ';base64,' . base64_encode(file_get_contents($uploadPath));
                        $imagePath = $uploadPath;
                    } elseif ($ext === 'pdf') {
                        // PDF als PNG für Preview (Poppler)
                        $pngBase = $uploadPath . '_page';
                        $cmdPng = "pdftoppm -f 1 -l 1 -png '$uploadPath' '$pngBase'";
                        shell_exec($cmdPng);
                        $pngPath = $pngBase . '-1.png';
                        if (file_exists($pngPath)) {
                            $filePreview = 'data:image/png;base64,' . base64_encode(file_get_contents($pngPath));
                            $imagePath = $pngPath; // Cropper & Bereichs-OCR arbeiten auf dem PNG!
                        } else {
                            $filePreview = null;
                            $imagePath = null;
                            $error = 'PDF-Preview fehlgeschlagen (Poppler installiert?)';
                        }
                    }

                    // OCR-Script aufrufen (egal ob Original oder PDF)
                    if ($imagePath) {
                        $venvPython = '/home/web/tekath-control/ocr/venv/bin/python';
                        $cmd = "$venvPython /home/web/tekath-control/ocr/ocr_fahrzeugschein.py '$imagePath' 2>&1";
                        $output = shell_exec($cmd);

                        $ocrTextFile = $imagePath . ".txt";
                        if (file_exists($ocrTextFile)) {
                            $ocrResult = file_get_contents($ocrTextFile);
                            $shellOutput = $output;
                        } else {
                            $error = "Es wurde kein Text erkannt oder das OCR-Skript hat kein Ergebnis erzeugt.";
                            $shellOutput = $output;
                        }
                    }
                } catch (\Exception $e) {
                    $error = "Fehler beim Datei-Upload oder OCR: " . $e->getMessage();
                }
            } else {
                $error = "Ungültiges Dateiformat. Erlaubt: PDF, JPG, JPEG, PNG.";
            }
        }

        return $this->render('ocr/fahrzeugschein.html.twig', [
            'ocrResult' => $ocrResult,
            'error' => $error,
            'filePreview' => $filePreview,
            'shellOutput' => $shellOutput,
            'imagePath' => $imagePath,
        ]);
    }

    #[Route('/ocr/fahrzeugschein/area', name: 'app_ocr_fahrzeugschein_area', methods: ['POST'])]
    public function ocrArea(Request $request): Response
    {
        $data = json_decode($request->getContent(), true);
        $imagePath = $data['imagePath'] ?? null;
        $x = (int)($data['x'] ?? 0);
        $y = (int)($data['y'] ?? 0);
        $w = (int)($data['width'] ?? 0);
        $h = (int)($data['height'] ?? 0);

        if (!$imagePath || !file_exists($imagePath) || !$w || !$h) {
            return $this->json(['error' => 'Ungültige Eingabe oder Bild nicht gefunden!']);
        }

        // Bereich als neues temporäres Bild speichern (immer PNG als Output)
        $croppedPath = sys_get_temp_dir() . '/' . uniqid('ocr_crop_') . '.png';
        $cmdCrop = "/usr/bin/convert '$imagePath' -crop {$w}x{$h}+{$x}+{$y} +repage '$croppedPath'";
        shell_exec($cmdCrop);

        // OCR auf den Cropped-Bereich
        $venvPython = '/home/web/tekath-control/ocr/venv/bin/python';
        $ocrScript = '/home/web/tekath-control/ocr/ocr_fahrzeugschein.py';
        $cmdOcr = "$venvPython $ocrScript '$croppedPath' 2>&1";
        $output = shell_exec($cmdOcr);

        $txtFile = $croppedPath . '.txt';
        if (file_exists($txtFile)) {
            $result = file_get_contents($txtFile);
            @unlink($croppedPath);
            @unlink($txtFile);
            return $this->json(['text' => $result]);
        } else {
            @unlink($croppedPath);
            return $this->json(['error' => 'Kein Text erkannt! ' . $output]);
        }
    }
}
