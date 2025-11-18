<?php

namespace App\Controller;

use App\Entity\UserRoles;
use App\Form\UserRolesType;
use App\Repository\UserRolesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;


#[Route('/admin/user-roles')]
final class UserRolesController extends AbstractController
{
    #[Route(name: 'app_user_roles_index', methods: ['GET'])]
    public function index(UserRolesRepository $userRolesRepository): Response
    {
        return $this->render('user_roles/index.html.twig', [
            'user_roles' => $userRolesRepository->findAll(),
            'page_title' => 'User Roles',
            'page_description' => 'Manage user roles and permissions.',
        ]);
    }


    #[Route('/sort', name: 'app_user_roles_sort', methods: ['POST'])]
public function sort(Request $request, EntityManagerInterface $em): JsonResponse
{
    if (!$this->isGranted('ROLE_ADMIN')) {
        return new JsonResponse(['error' => 'Unauthorized'], 403);
    }

    $data = json_decode($request->getContent(), true);
    $ids = $data['ids'] ?? [];

    if (empty($ids)) {
        return new JsonResponse(['error' => 'Keine IDs erhalten'], 400);
    }

    foreach ($ids as $index => $id) {
        $role = $em->getRepository(UserRoles::class)->find($id);
        if ($role) {
            $role->setHierarchy($index + 1);
        }
    }

    $em->flush();

    return new JsonResponse(['status' => 'ok']);
}



    #[Route('/new', name: 'app_user_roles_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $userRole = new UserRoles();
        $form = $this->createForm(UserRolesType::class, $userRole);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($userRole);
            $entityManager->flush();

            return $this->redirectToRoute('app_user_roles_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user_roles/new.html.twig', [
            'user_role' => $userRole,
            'form' => $form,
            'page_title' => 'Create User Role',
            'page_description' => 'Define a new user role and its permissions.',
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_user_roles_show', methods: ['GET'])]
    public function show(UserRoles $userRole): Response
    {
        return $this->render('user_roles/show.html.twig', [
            'user_role' => $userRole,
            'page_title' => 'User Role Details',
            'page_description' => 'View details of the selected user role.',
        ]);
    }

    #[Route('/{id<\d+>}/edit', name: 'app_user_roles_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, UserRoles $userRole, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(UserRolesType::class, $userRole);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_user_roles_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user_roles/edit.html.twig', [
            'user_role' => $userRole,
            'form' => $form,
            'page_title' => 'Edit User Role',
            'page_description' => 'Modify the selected user role and its permissions.',
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_user_roles_delete', methods: ['POST'])]
    public function delete(Request $request, UserRoles $userRole, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$userRole->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($userRole);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_user_roles_index', [], Response::HTTP_SEE_OTHER);
    }
}
