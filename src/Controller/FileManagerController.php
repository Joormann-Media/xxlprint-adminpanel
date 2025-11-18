<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

final class FileManagerController extends AbstractController
{
    #[Route('/file-manager/list', name: 'file_manager_list', methods: ['GET'])]
    public function list(Request $request): JsonResponse
    {
        $dir = $request->query->get('dir', ''); // Relativer Pfad
        $root = $this->getParameter('kernel.project_dir'); // Projekt-Root

        $baseDir = realpath($root);
        $currentDir = realpath($root . DIRECTORY_SEPARATOR . $dir) ?: $baseDir;

        // Sicherheits-Check: Nicht außerhalb root browsen lassen!
        if (!$currentDir || strpos($currentDir, $baseDir) !== 0) {
            return $this->json(['error' => 'Invalid directory'], 400);
        }

        $dirs = [];
        $files = [];
        foreach (scandir($currentDir) as $item) {
            if ($item === '.' || $item === '..') continue;
            $path = $currentDir . DIRECTORY_SEPARATOR . $item;
            $relPath = ltrim(str_replace($baseDir, '', $path), DIRECTORY_SEPARATOR);
            if (is_dir($path)) {
                $dirs[] = ['name' => $item, 'path' => $relPath];
            } else {
                $files[] = ['name' => $item, 'path' => $relPath];
            }
        }

        return $this->json([
            'dirs' => $dirs,
            'files' => $files,
            'parent' => $dir ? dirname($dir) : null,
        ]);
    }

    #[Route('/file-manager/explorer', name: 'app_file_manager_explorer', methods: ['GET'])]
    public function explorer(): Response
    {
        // Gibt NUR das Modal-Partial zurück!
        return $this->render('file_manager/_explorer.html.twig');
    }
}
