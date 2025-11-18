<?php

namespace App\Controller;

use App\Entity\UserGroups;
use App\Form\UserGroupsType;
use App\Repository\UserGroupsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
// Removed duplicate import
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Filesystem\Filesystem;

#[Route('/admin/user-groups')]
final class UserGroupsController extends AbstractController
{
    #[Route(name: 'app_user_groups_index', methods: ['GET'])]
    public function index(UserGroupsRepository $userGroupsRepository): Response
    {
        return $this->render('user_groups/index.html.twig', [
            'user_groups' => $userGroupsRepository->findAll(),
            'page_title' => 'User Groups',
            'page_description' => 'Manage user groups and their permissions.',
        ]);
    }

    #[Route('/new', name: 'app_user_groups_new', methods: ['GET', 'POST'])]
public function new(Request $request, EntityManagerInterface $entityManager): Response
{
    $userGroup = new UserGroups();
    $form = $this->createForm(UserGroupsType::class, $userGroup);
    $form->handleRequest($request);

    // Neu:
$baseDir = bin2hex(random_bytes(6));
$userGroup->setBaseDir($baseDir);

    if ($form->isSubmitted() && $form->isValid()) {
        $userGroupsDir = bin2hex(random_bytes(6));
        $userGroup->setBaseDir($userGroupsDir);

        $entityManager->persist($userGroup);
        $entityManager->flush();

  
        

        // Verzeichnis anlegen
        $fs = new \Symfony\Component\Filesystem\Filesystem();
              // Verzeichnis erstellen fallen es nicht existiert
              $rootPath = $this->getParameter('kernel.project_dir') . '/public/user_groups';
              if (!$fs->exists($rootPath)) {
                  $fs->mkdir($rootPath, 0775);
              }
        $dirPath = $this->getParameter('kernel.project_dir') . '/public/user_groups/' . $baseDir;
        $fs->mkdir([
            $dirPath . '/private',
            $dirPath . '/public',
            $dirPath . '/avatar',
        ]);

        // Datei-Upload verarbeiten
        /** @var UploadedFile $logoFile */
        $logoFile = $form->get('groupLogo')->getData();
        if ($logoFile) {
            $safeFilename = pathinfo($logoFile->getClientOriginalName(), PATHINFO_FILENAME);
            $newFilename = $safeFilename . '-' . uniqid() . '.' . $logoFile->guessExtension();

            $logoFile->move($baseDir . '/avatar', $newFilename);

            $userGroup->setGroupLogo($newFilename);
        }

        $entityManager->persist($userGroup);
        $entityManager->flush();

        return $this->redirectToRoute('app_user_groups_index');
    }

    return $this->render('user_groups/new.html.twig', [
        'user_group' => $userGroup,
        'form' => $form,
        'user_groups_dir' => $baseDir,
        'page_title' => 'Create User Group',
        'page_description' => 'Create a new user group and assign permissions.',
    ]);
}


    #[Route('/{id<\d+>}', name: 'app_user_groups_show', methods: ['GET'])]
    public function show(UserGroups $userGroup): Response
    {
        return $this->render('user_groups/show.html.twig', [
            'user_group' => $userGroup,
            'page_title' => 'User Group Details',
            'page_description' => 'View details of the selected user group.',
        ]);
    }

    #[Route('/{id<\d+>}/edit', name: 'app_user_groups_edit', methods: ['GET', 'POST'])]
public function edit(Request $request, UserGroups $userGroup, EntityManagerInterface $entityManager): Response
{
    $form = $this->createForm(UserGroupsType::class, $userGroup);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {

        // 🖼️ Datei-Upload prüfen
        $logoFile = $form->get('groupLogo')->getData();

        if ($logoFile) {
            $newFilename = uniqid() . '.' . $logoFile->guessExtension();
            $uploadDir = $this->getParameter('kernel.project_dir') . '/public/user_groups/' . $userGroup->getBaseDir() . '/avatar';
            try {
                $logoFile->move($uploadDir, $newFilename);
                $userGroup->setGroupLogo($newFilename);
            } catch (\Exception $e) {
                $this->addFlash('danger', 'Fehler beim Hochladen des Logos: ' . $e->getMessage());
            }
        }

        $entityManager->flush();

        $this->addFlash('success', 'Benutzergruppe wurde aktualisiert.');
        return $this->redirectToRoute('app_user_groups_index');
    }

    return $this->render('user_groups/edit.html.twig', [
        'user_group' => $userGroup,
        'form' => $form,
        'page_title' => 'Edit User Group',
        'page_description' => 'Editiere diese Gruppe inklusive Mitglieder, Logo und Metadaten.',
    ]);
}

    #[Route('/{id<\d+>}', name: 'app_user_groups_delete', methods: ['POST'])]
    public function delete(Request $request, UserGroups $userGroup, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$userGroup->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($userGroup);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_user_groups_index', [], Response::HTTP_SEE_OTHER);
    }
}
