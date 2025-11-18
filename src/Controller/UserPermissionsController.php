<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\User1Type;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/user-permissions')]
final class UserPermissionsController extends AbstractController
{
    #[Route(name: 'app_user_permissions_index', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('user_permissions/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_user_permissions_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(User1Type::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_user_permissions_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user_permissions/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_user_permissions_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        return $this->render('user_permissions/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/{id<\d+>}/edit', name: 'app_user_permissions_edit', methods: ['GET', 'POST'])]
public function edit(Request $request, User $user, EntityManagerInterface $entityManager): Response
{
    // ✅ Bestehende Rollen & Benutzergruppen aus der Datenbank holen
    $existingRoles = $user->getRoles(); // Symfony speichert Rollen als Array
    $existingUserGroups = $user->getUsergroups() ? explode(',', $user->getUsergroups()) : [];

    // ✅ Formular erstellen & die vorhandenen Werte setzen
    $form = $this->createForm(User1Type::class, $user, [
        'existing_roles' => $existingRoles, // Übergebe die vorhandenen Rollen
        'existing_usergroups' => $existingUserGroups, // Übergebe vorhandene UserGroups
    ]);

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        // ✅ Speichern der neuen Werte
        $selectedRoles = $form->get('roles')->getData();
        $selectedUserGroups = $form->get('usergroups')->getData();

        // ✅ Setzen der neuen Werte im User-Objekt
        $user->setRoles($selectedRoles);
        $user->setUsergroups(implode(',', $selectedUserGroups));

        $entityManager->persist($user);
        $entityManager->flush();

        return $this->redirectToRoute('app_user_permissions_index', [], Response::HTTP_SEE_OTHER);
    }

    
    return $this->render('user_permissions/edit.html.twig', [
        'user' => $user,
        'form' => $form,
        'page_title' => 'Edit User',
    ]);
}


    #[Route('/{id<\d+>}', name: 'app_user_permissions_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_user_permissions_index', [], Response::HTTP_SEE_OTHER);
    }
}
