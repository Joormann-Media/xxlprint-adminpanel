<?php

namespace App\Controller\Admin;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Repository\UserRepository;

#[Route('/admin/user-files')]
#[IsGranted('ROLE_USERADMIN')]
class AdminUserFileController extends AbstractController
{
    private array $allowedSubdirs = ['files', 'avatar', 'public', 'private'];

    #[Route('', name: 'admin_user_file_index')]
    public function listUsers(UserRepository $userRepo): Response
    {
        $users = $userRepo->findAll();

        return $this->render('admin/user_files/index.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/{userId}/{subdir}', name: 'admin_user_files', defaults: ['subdir' => 'files'])]
    public function index(UserRepository $userRepo, int $userId, string $subdir): Response
    {
        if (!in_array($subdir, $this->allowedSubdirs, true)) {
            throw $this->createAccessDeniedException('Unzulässiges Verzeichnis.');
        }

        $user = $userRepo->find($userId);
        if (!$user) {
            throw $this->createNotFoundException('Benutzer nicht gefunden.');
        }

        $basePath = $this->getParameter('user_data_base_path');
        $dir = $basePath . '/' . $user->getUserDir() . '/' . $subdir;

        if (!file_exists($dir)) {
            mkdir($dir, 0775, true);
        }

        $files = array_diff(scandir($dir), ['.', '..']);

        return $this->render('admin/user_files/index.html.twig', [
            'files' => $files,
            'user' => $user,
            'userDir' => $user->getUserDir(),
            'fullPath' => $dir,
            'publicPath' => '/user_data/' . $user->getUserDir() . '/' . $subdir . '/',
            'subdir' => $subdir,
            'allowedSubdirs' => $this->allowedSubdirs,
        ]);
    }

    #[Route('/upload/{userId}/{subdir}', name: 'admin_user_file_upload', methods: ['POST'])]
    public function upload(Request $request, UserRepository $userRepo, int $userId, string $subdir): JsonResponse
    {
        if (!in_array($subdir, $this->allowedSubdirs, true)) {
            return new JsonResponse(['error' => 'Ungültiger Ordner'], 400);
        }

        $user = $userRepo->find($userId);
        if (!$user) {
            return new JsonResponse(['error' => 'Benutzer nicht gefunden'], 404);
        }

        $basePath = $this->getParameter('user_data_base_path');
        $dir = $basePath . '/' . $user->getUserDir() . '/' . $subdir;

        if (!file_exists($dir)) {
            mkdir($dir, 0775, true);
        }

        $file = $request->files->get('file');
        if (!$file) {
            return new JsonResponse(['error' => 'Keine Datei empfangen'], 400);
        }

        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = preg_replace('/[^a-zA-Z0-9-_\.]/', '_', $originalFilename);
        $newFilename = $safeFilename . '-' . uniqid() . '.' . $file->guessExtension();

        try {
            $file->move($dir, $newFilename);
        } catch (FileException $e) {
            return new JsonResponse(['error' => 'Upload fehlgeschlagen'], 500);
        }

        return new JsonResponse(['success' => true, 'filename' => $newFilename]);
    }

    #[Route('/delete/{userId}/{subdir}/{filename}', name: 'admin_user_file_delete', methods: ['POST'])]
    public function delete(UserRepository $userRepo, int $userId, string $subdir, string $filename): RedirectResponse
    {
        if (!in_array($subdir, $this->allowedSubdirs, true)) {
            throw $this->createAccessDeniedException('Unzulässiger Ordner.');
        }

        $user = $userRepo->find($userId);
        if (!$user) {
            throw $this->createNotFoundException('Benutzer nicht gefunden.');
        }

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

        return $this->redirectToRoute('admin_user_files', [
            'userId' => $userId,
            'subdir' => $subdir
        ]);
    }
} 
