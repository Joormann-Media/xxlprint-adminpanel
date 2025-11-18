<?php
// src/Controller/Admin/DevMenuDebugController.php

namespace App\Controller\Admin;

use App\Repository\MenuRepository;
use App\Repository\MenuSubMenuRepository;
use App\Repository\MenuItemRepository;
use App\Service\MenuService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DevMenuDebugController extends AbstractController
{
    #[Route('/admin/dev/menu-overview', name: 'dev_menu_overview')]
    public function index(
        MenuRepository $menuRepo,
        MenuSubMenuRepository $subMenuRepo,
        MenuItemRepository $itemRepo,
        MenuService $menuService // 🧠 Menüservice dazunehmen
    ): Response {
        $menus = $menuRepo->findAll();
        $subMenus = $subMenuRepo->findAll();
        $items = $itemRepo->findAll();

        // Hole die strukturierte Menüstruktur (inkl. Debug-Infos)
        $structured = $menuService->getStructuredMenu(
            debugMode: true,
            showEmptyMenus: true
        );
        foreach ($structured['menus'] as $menu) {
            if ($menu['id'] === 13) {
                dump('🧪 TEST-MENÜ IST DRIN:', $menu);
            }
        }
        return $this->render('dev/menu_overview.html.twig', [
            'menus' => $menus,
            'subMenus' => $subMenus,
            'items' => $items,
            'structured' => $structured, // 🧩 Jetzt im Template verfügbar
            'debug' => $result['debug'] ?? null
        ]);
    }

    #[Route('/admin/dev/registerlogtest', name: 'dev_register_log_test')]
    public function indexRegisterLogTest(): Response
    {
        return $this->render('dev/registerlogtest.html.twig');
    }
}