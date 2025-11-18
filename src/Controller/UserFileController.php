<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Psr\Log\LoggerInterface;

#[Route('/admin/my-files')]
#[IsGranted('ROLE_USER')]
class UserFileController extends AbstractController
{
    private array $allowedSubdirs = ['files', 'avatar', 'public', 'private'];

    #[Route('/{subdir}', name: 'app_user_files', defaults: ['subdir' => 'files'])]
    public function index(string $subdir): Response
    {
        if (!in_array($subdir, $this->allowedSubdirs, true)) {
            throw $this->createAccessDeniedException('Unzulässiges Verzeichnis.');
        }

        $user = $this->getUser();
        $basePath = $this->getParameter('user_data_base_path');
        $dir = $basePath . '/' . $user->getUserDir() . '/' . $subdir;

        if (!file_exists($dir)) {
            mkdir($dir, 0775, true);
        }

        $files = array_diff(scandir($dir), ['.', '..']);

        return $this->render('user_files/index.html.twig', [
            'files' => $files,
            'userDir' => $user->getUserDir(),
            'fullPath' => $dir,
            'publicPath' => '/user_data/' . $user->getUserDir() . '/' . $subdir . '/',
            'subdir' => $subdir,
            'allowedSubdirs' => $this->allowedSubdirs,
            'page_title' => 'Datei-Verwaltung',
            'page_description' => 'Hier können Sie Ihre Dateien verwalten.',
        ]);
    }

    #[Route('/upload/{subdir}', name: 'app_user_file_upload', methods: ['POST'])]
    public function upload(Request $request, string $subdir, LoggerInterface $logger): JsonResponse
    {
        if (!in_array($subdir, $this->allowedSubdirs, true)) {
            $logger->error('Invalid subdir provided for upload.', ['subdir' => $subdir]);
            return new JsonResponse(['error' => 'Ungültiger Ordner'], 400);
        }

        $user = $this->getUser();
        $basePath = $this->getParameter('user_data_base_path');
        $dir = $basePath . '/' . $user->getUserDir() . '/' . $subdir;

        if (!file_exists($dir)) {
            mkdir($dir, 0775, true);
        }

        $file = $request->files->get('file');

        if (!$file) {
            $logger->error('No file received in the request.');
            return new JsonResponse(['error' => 'Keine Datei empfangen'], 400);
        }

        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = preg_replace('/[^a-zA-Z0-9-_\.]/', '_', $originalFilename);
        $extension = $file->guessExtension() ?? pathinfo($file->getClientOriginalName(), PATHINFO_EXTENSION);
        $newFilename = $safeFilename . '-' . uniqid() . '.' . $extension;

        try {
            $file->move($dir, $newFilename);
            $logger->info('File uploaded successfully.', [
                'userDir' => $user->getUserDir(),
                'filename' => $newFilename,
                'subdir' => $subdir,
            ]);
        } catch (FileException $e) {
            $logger->error('File upload failed.', ['error' => $e->getMessage()]);
            return new JsonResponse(['error' => 'Upload fehlgeschlagen'], 500);
        }

        return new JsonResponse(['success' => true, 'filename' => $newFilename]);
    }

    #[Route('/delete/{subdir}/{filename}', name: 'app_user_file_delete', methods: ['POST'])]
    public function delete(string $subdir, string $filename): RedirectResponse
    {
        if (!in_array($subdir, $this->allowedSubdirs, true)) {
            throw $this->createAccessDeniedException('Unzulässiger Ordner.');
        }

        $user = $this->getUser();
        $basePath = $this->getParameter('user_data_base_path');
        $dir = $basePath . '/' . $user->getUserDir() . '/' . $subdir;
        $path = $dir . '/' . $filename;

        if (file_exists($path)) {
            $fs = new Filesystem();
            $fs->remove($path);
            $this->addFlash('success', 'Datei wurde gelöscht.');
        } else {
            $this->addFlash('warning', 'Datei nicht gefunden.');
        }

        return $this->redirectToRoute('app_user_files', ['subdir' => $subdir]);
    }
    
}