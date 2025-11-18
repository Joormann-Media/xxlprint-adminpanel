<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use App\Repository\UserGroupsRepository;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/user')]
final class UserController extends AbstractController
{
    // 🔐 Zentrale Rollenprüfung
    private function isUserAdmin(User $user): bool
    {
        $allRoles = [];

        foreach ($user->getRoles() as $role) {
            $parts = array_map('trim', explode(',', $role));
            foreach ($parts as $r) {
                if (!in_array($r, $allRoles, true)) {
                    $allRoles[] = strtoupper($r);
                }
            }
        }

        return in_array('ROLE_USERADMIN', $allRoles, true);
    }

    #[Route(name: 'app_user_index_or_show', methods: ['GET'])]
    public function indexOrShow(UserRepository $userRepository, UserGroupsRepository $userGroupsRepository): Response
    {
        $user = $this->getUser();

        if (!$user instanceof User) {
            throw $this->createAccessDeniedException('User not logged in.');
        }

        if ($this->isUserAdmin($user)) {
            return $this->render('user/index.html.twig', [
                'users' => $userRepository->findAll(),
                'page_title' => 'Benutzerverwaltung',
                'userGroupMap' => $this->getUserGroupMap($userGroupsRepository),
            ]);
        }

        return $this->redirectToRoute('app_user_show', [
            'id' => $user->getId(),
            'page_title' => 'Mein Profil',
        ]);
    }

    #[Route(name: 'app_user_index', methods: ['GET'])]
    public function index(UserRepository $userRepository, UserGroupsRepository $userGroupsRepository): Response
    {
        return $this->render('user/index.html.twig', [
            'users' => $userRepository->findAll(),
            'page_title' => 'Benutzerverwaltung',
            'userGroupMap' => $this->getUserGroupMap($userGroupsRepository),
        ]);
    }

    #[Route('/new', name: 'app_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_user_index');
        }

        return $this->render('user/new.html.twig', [
            'user' => $user,
            'form' => $form,
            'page_title' => 'Benutzerverwaltung - Neuer Benutzer',
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_user_show', methods: ['GET'])]
    public function show(User $user, UserGroupsRepository $userGroupsRepository): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,
            'userGroupMap' => $this->getUserGroupMap($userGroupsRepository),
            'page_title' => 'Benutzeraccount',
        ]);
    }

    #[Route('/{id<\d+>}/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_user_index');
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
            'page_title' => 'Benutzer bearbeiten',
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_user_index');
    }

    #[Route('/sort', name: 'app_user_sort', methods: ['POST'])]
    public function sort(Request $request, UserRepository $repo, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if (!isset($data['items']) || !is_array($data['items'])) {
            return new JsonResponse(['error' => 'Ungültige Daten'], 400);
        }

        foreach ($data['items'] as $entry) {
            $user = $repo->find($entry['id']);
            if ($user) {
                $user->setSortOrder($entry['sortOrder']);
            }
        }

        $em->flush();
        return new JsonResponse(['status' => 'ok']);
    }

    #[Route('/bulk-delete', name: 'app_user_bulk_delete', methods: ['POST'])]
    public function bulkDelete(Request $request, EntityManagerInterface $entityManager, UserRepository $userRepository): Response
    {
        $userIds = $request->request->all('users');
        $csrfToken = $request->request->get('_token');

        if ($this->isCsrfTokenValid('bulk_delete', $csrfToken) && !empty($userIds)) {
            $users = $userRepository->findBy(['id' => $userIds]);

            foreach ($users as $user) {
                $entityManager->remove($user);
            }

            $entityManager->flush();
            $this->addFlash('success', 'Ausgewählte Benutzer wurden gelöscht.');
        } else {
            $this->addFlash('error', 'Ungültiges CSRF-Token oder keine Benutzer ausgewählt.');
        }

        return $this->redirectToRoute('app_user_index');
    }
    private function getUserGroupMap(UserGroupsRepository $repo): array
{
    $map = [];
    foreach ($repo->findAll() as $group) {
        $map[$group->getId()] = $group->getGroupName();
    }
    return $map;
}
}
