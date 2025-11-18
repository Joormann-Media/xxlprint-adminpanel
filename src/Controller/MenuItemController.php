<?php

namespace App\Controller;

use App\Entity\MenuItem;
use App\Form\MenuItemType;
use App\Repository\MenuItemRepository;
use App\Repository\MenuRepository;
use App\Repository\MenuSubMenuRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/menu-item')]
final class MenuItemController extends AbstractController
{
    #[Route(name: 'app_menu_item_index', methods: ['GET'])]
    public function index(
        MenuItemRepository $menuItemRepository,
        MenuRepository $menuRepository,
        MenuSubMenuRepository $menuSubMenuRepository
    ): Response {
        $menuItems = $menuItemRepository->findBy([], ['menuId' => 'ASC', 'subMenuId' => 'ASC', 'sortOrder' => 'ASC', 'id' => 'ASC']);


        $menuNames = [];
        $subMenuNames = [];

        foreach ($menuItems as $item) {
            if ($item->getMenuId() && !isset($menuNames[$item->getMenuId()])) {
                $menu = $menuRepository->find($item->getMenuId());
                $menuNames[$item->getMenuId()] = $menu ? $menu->getName() : 'Unbekanntes Menü';
            }

            if ($item->getSubMenuId() && !isset($subMenuNames[$item->getSubMenuId()])) {
                $sub = $menuSubMenuRepository->find($item->getSubMenuId());
                $subMenuNames[$item->getSubMenuId()] = $sub ? $sub->getName() : 'Unbekanntes Submenü';
            }
        }

        return $this->render('menu_item/index.html.twig', [
            'menu_items' => $menuItems,
            'menu_names' => $menuNames,
            'submenu_names' => $subMenuNames,
            'page_title' => 'Menu-Items',
        ]);
    }

    #[Route('/sort', name: 'app_menu_item_sort', methods: ['POST'])]
public function sort(Request $request, MenuItemRepository $repo, EntityManagerInterface $em): JsonResponse
{
    $data = json_decode($request->getContent(), true);
    if (!isset($data['items']) || !is_array($data['items'])) {
        return new JsonResponse(['error' => 'Ungültige Daten'], 400);
    }

    foreach ($data['items'] as $entry) {
        $item = $repo->find($entry['id']);
        if ($item) {
            $item->setSortOrder($entry['sortOrder']);
        }
    }

    $em->flush();
    return new JsonResponse(['status' => 'ok']);
}


    #[Route('/new', name: 'app_menu_item_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $menuItem = new MenuItem();
        $form = $this->createForm(MenuItemType::class, $menuItem);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($menuItem);
            $entityManager->flush();

            return $this->redirectToRoute('app_menu_item_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('menu_item/new.html.twig', [
            'menu_item' => $menuItem,
            'form' => $form,
            'page_title' => 'New Menu-Item',
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_menu_item_show', methods: ['GET'])]
    public function show(MenuItem $menuItem): Response
    {
        return $this->render('menu_item/show.html.twig', [
            'menu_item' => $menuItem,
            'page_title' => 'Menu-Item Show',
        ]);
    }

    #[Route('/{id<\d+>}/edit', name: 'app_menu_item_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, MenuItem $menuItem, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(MenuItemType::class, $menuItem);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_menu_item_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('menu_item/edit.html.twig', [
            'menu_item' => $menuItem,
            'form' => $form,
            'page_title' => 'Edit Menu-Item',
        ]);
    }

    #[Route('/delete/{id<\d+>}', name: 'app_menu_item_delete', methods: ['POST'])]
    public function delete(Request $request, EntityManagerInterface $entityManager, MenuItemRepository $menuItemRepository, int $id): Response
    {
        $menuItem = $menuItemRepository->find($id);

        if (!$menuItem) {
            $this->addFlash('warning', 'Das Menü-Item wurde nicht gefunden.');
            return $this->redirectToRoute('app_menu_item_index');
        }

        if ($this->isCsrfTokenValid('delete' . $menuItem->getId(), $request->request->get('_token'))) {
            $entityManager->remove($menuItem);
            $entityManager->flush();
            $this->addFlash('success', 'Menü-Item erfolgreich gelöscht.');
        } else {
            $this->addFlash('error', 'Ungültiges CSRF-Token.');
        }

        return $this->redirectToRoute('app_menu_item_index');
    }

    #[Route('/bulk-delete', name: 'app_menu_item_bulk_delete', methods: ['POST'])]
    public function bulkDelete(Request $request, EntityManagerInterface $entityManager, MenuItemRepository $menuItemRepository): Response
    {
        $menuItemIds = $request->request->all('menu_items');
        $csrfToken = $request->request->get('_token');

        if ($this->isCsrfTokenValid('bulk_delete', $csrfToken) && !empty($menuItemIds) && is_array($menuItemIds)) {
            $menuItems = $menuItemRepository->findBy(['id' => $menuItemIds]);

            if (!empty($menuItems)) {
                foreach ($menuItems as $menuItem) {
                    $entityManager->remove($menuItem);
                }
                $entityManager->flush();
                $this->addFlash('success', 'Ausgewählte Menüeinträge wurden gelöscht.');
            } else {
                $this->addFlash('warning', 'Keine gültigen Menüeinträge gefunden.');
            }
        } else {
            $this->addFlash('error', 'Ungültiges CSRF-Token oder keine Menüeinträge ausgewählt.');
        }

        return $this->redirectToRoute('app_menu_item_index');
    }

}
