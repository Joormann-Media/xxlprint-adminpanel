<?php

namespace App\Controller;

use App\Entity\MenuSubMenu;
use App\Form\MenuSubMenuType;
use App\Repository\MenuSubMenuRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/sub-menu')]
final class MenuSubMenuController extends AbstractController
{
    #[Route(name: 'app_menu_sub_menu_index', methods: ['GET'])]
    public function index(MenuSubMenuRepository $menuSubMenuRepository): Response
    {
        return $this->render('menu_sub_menu/index.html.twig', [
            'menu_sub_menus' => $menuSubMenuRepository->findAll(),
            'page_title' => 'Submenus',
        ]);
    }

    #[Route('/new', name: 'app_menu_sub_menu_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $menuSubMenu = new MenuSubMenu();
        $form = $this->createForm(MenuSubMenuType::class, $menuSubMenu);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($menuSubMenu);
            $entityManager->flush();

            return $this->redirectToRoute('app_menu_sub_menu_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('menu_sub_menu/new.html.twig', [
            'menu_sub_menu' => $menuSubMenu,
            'form' => $form,
            'page_title' => 'New Submenu',
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_menu_sub_menu_show', methods: ['GET'])]
    public function show(int $id, MenuSubMenuRepository $menuSubMenuRepository): Response
    {
        $menuSubMenu = $menuSubMenuRepository->find($id);
        if (!$menuSubMenu) {
            throw $this->createNotFoundException('Das Untermenü wurde nicht gefunden.');
        }

        return $this->render('menu_sub_menu/show.html.twig', [
            'menu_sub_menu' => $menuSubMenu,
            'page_title' => 'Submenu Show',
        ]);
    }

    #[Route('/{id<\d+>}/edit', name: 'app_menu_sub_menu_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, int $id, MenuSubMenuRepository $menuSubMenuRepository, EntityManagerInterface $entityManager): Response
    {
        $menuSubMenu = $menuSubMenuRepository->find($id);
        if (!$menuSubMenu) {
            throw $this->createNotFoundException('Das Untermenü wurde nicht gefunden.');
        }

        $form = $this->createForm(MenuSubMenuType::class, $menuSubMenu);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_menu_sub_menu_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('menu_sub_menu/edit.html.twig', [
            'menu_sub_menu' => $menuSubMenu,
            'form' => $form,
            'page_title' => 'Edit Submenu',
        ]);
    }

    #[Route('/delete/{id<\d+>}', name: 'app_menu_sub_menu_delete', methods: ['POST'])]
    public function delete(Request $request, EntityManagerInterface $entityManager, MenuSubMenuRepository $menuSubMenuRepository, int $id): Response
    {
        $menuSubMenu = $menuSubMenuRepository->find($id);

        if (!$menuSubMenu) {
            $this->addFlash('warning', 'Das Untermenü wurde nicht gefunden.');
            return $this->redirectToRoute('app_menu_sub_menu_index');
        }

        if ($this->isCsrfTokenValid('delete' . $menuSubMenu->getId(), $request->request->get('_token'))) {
            $entityManager->remove($menuSubMenu);
            $entityManager->flush();
            $this->addFlash('success', 'Untermenü erfolgreich gelöscht.');
        } else {
            $this->addFlash('error', 'Ungültiges CSRF-Token.');
        }

        return $this->redirectToRoute('app_menu_sub_menu_index');
    }

    #[Route('/bulk-delete', name: 'app_menu_sub_menu_bulk_delete', methods: ['POST'])]
    public function bulkDelete(Request $request, EntityManagerInterface $entityManager, MenuSubMenuRepository $menuSubMenuRepository): Response
    {
        $menuSubMenuIds = $request->request->all('menu_sub_menus');
        $csrfToken = $request->request->get('_token');

        if ($this->isCsrfTokenValid('bulk_delete', $csrfToken) && !empty($menuSubMenuIds) && is_array($menuSubMenuIds)) {
            $menuSubMenus = $menuSubMenuRepository->findBy(['id' => $menuSubMenuIds]);

            if (!empty($menuSubMenus)) {
                foreach ($menuSubMenus as $menuSubMenu) {
                    $entityManager->remove($menuSubMenu);
                }
                $entityManager->flush();
                $this->addFlash('success', 'Ausgewählte Untermenüs wurden gelöscht.');
            } else {
                $this->addFlash('warning', 'Keine gültigen Untermenüs gefunden.');
            }
        } else {
            $this->addFlash('error', 'Ungültiges CSRF-Token oder keine Untermenüs ausgewählt.');
        }

        return $this->redirectToRoute('app_menu_sub_menu_index');
    }
}
