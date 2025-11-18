<?php

namespace App\Controller;

use App\Entity\Menu;
use App\Form\MenuType;
use App\Repository\MenuRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/menu')]
final class MenuController extends AbstractController
{
    #[Route(name: 'app_menu_index', methods: ['GET'])]
    public function index(MenuRepository $menuRepository): Response
    {
        return $this->render('menu/index.html.twig', [
            'menus' => $menuRepository->findAll(),
            'page_title' => 'Menus',
        ]);
    }

    #[Route('/new', name: 'app_menu_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $menu = new Menu();
        $form = $this->createForm(MenuType::class, $menu);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($menu);
            $entityManager->flush();

            return $this->redirectToRoute('app_menu_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('menu/new.html.twig', [
            'menu' => $menu,
            'form' => $form,
            'page_title' => 'New Menu',
            
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_menu_show', methods: ['GET'])]
    public function show(Menu $menu): Response
    {
        return $this->render('menu/show.html.twig', [
            'menu' => $menu,
            'page_title' => 'Menu Show',
        ]);
    }

    #[Route('/{id<\d+>}/edit', name: 'app_menu_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Menu $menu, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(MenuType::class, $menu);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_menu_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('menu/edit.html.twig', [
            'menu' => $menu,
            'form' => $form,
            'page_title' => 'Edit Menu',
        ]);
    }

    #[Route('/delete/{id<\d+>}', name: 'app_menu_delete', methods: ['POST'])]
    public function delete(Request $request, EntityManagerInterface $entityManager, MenuRepository $menuRepository, int $id): Response
    {
        $menu = $menuRepository->find($id);

        if (!$menu) {
            $this->addFlash('warning', 'Das Menü wurde nicht gefunden.');
            return $this->redirectToRoute('app_menu_index');
        }

        if ($this->isCsrfTokenValid('delete' . $menu->getId(), $request->request->get('_token'))) {
            $entityManager->remove($menu);
            $entityManager->flush();
            $this->addFlash('success', 'Das Menü wurde erfolgreich gelöscht.');
        } else {
            $this->addFlash('error', 'Ungültiges CSRF-Token.');
        }

        return $this->redirectToRoute('app_menu_index');
    }

    #[Route('/bulk-delete', name: 'app_menu_bulk_delete', methods: ['POST'])]
    public function bulkDelete(Request $request, EntityManagerInterface $entityManager, MenuRepository $menuRepository): Response
    {
        $menuIds = $request->request->all('menus'); // Use 'all' method to get array of menu IDs
        $csrfToken = $request->request->get('_token');

        if ($this->isCsrfTokenValid('bulk_delete', $csrfToken) && !empty($menuIds)) {
            $menus = $menuRepository->findBy(['id' => $menuIds]);

            if (!empty($menus)) {
                foreach ($menus as $menu) {
                    $entityManager->remove($menu);
                }
                $entityManager->flush();
                $this->addFlash('success', 'Ausgewählte Menüs wurden gelöscht.');
            } else {
                $this->addFlash('warning', 'Keine gültigen Menüs gefunden.');
            }
        } else {
            $this->addFlash('error', 'Ungültiges CSRF-Token oder keine Menüs ausgewählt.');
        }

        return $this->redirectToRoute('app_menu_index');
    }
}
