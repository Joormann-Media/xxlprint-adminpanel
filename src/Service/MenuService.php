<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\MenuRepository;
use App\Repository\MenuItemRepository;
use App\Repository\MenuSubMenuRepository;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Routing\RouterInterface;

class MenuService
{
    public function __construct(
        private readonly MenuRepository $menuRepository,
        private readonly MenuItemRepository $menuItemRepository,
        private readonly MenuSubMenuRepository $menuSubMenuRepository,
        private readonly AuthorizationCheckerInterface $authorizationChecker,
        private readonly Security $security,
        private readonly RouterInterface $router
    ) {}

    private function hasAccess(?string $minRoleRaw, array $userRoles, bool $debugMode = false, ?string $context = null, ?array &$debugLog = []): bool
    {
        if (!$minRoleRaw) return true;

        $minRoleRaw = str_replace(';', ',', $minRoleRaw);
        $requiredRoles = array_filter(array_map('trim', explode(',', $minRoleRaw)));

        $requiredRoles = array_map(function ($role) {
            $role = strtoupper($role);
            return str_starts_with($role, 'ROLE_') ? $role : 'ROLE_' . $role;
        }, $requiredRoles);

        $hasAccess = !empty(array_intersect($requiredRoles, $userRoles));

        if ($debugMode && $context) {
            $debugLog[] = [
                'context' => $context,
                'requiredRoles' => $requiredRoles,
                'userRoles' => $userRoles,
                'access' => $hasAccess
            ];
        }

        return $hasAccess;
    }

    public function getStructuredMenu(bool $debugMode = false, ?array $overrideRoles = null, bool $showEmptyMenus = false): array
    {
        if (!$this->authorizationChecker->isGranted('IS_AUTHENTICATED_FULLY') && !$overrideRoles) {
            return [];
        }

        $user = $this->security->getUser();
        if (!$user instanceof User && !$overrideRoles) {
            return [];
        }

        $userRoles = $overrideRoles ?? $user->getRoles();

        $menuEntities = $this->menuRepository->findBy([], ['sortOrder' => 'ASC']);
        $subMenuEntities = $this->menuSubMenuRepository->findBy([], ['sortOrder' => 'ASC']);
        $menuItems = $this->menuItemRepository->findBy([], ['sortOrder' => 'ASC']);

        $menuAccess = [];
        $subMenuAccess = [];
        $debugLog = [];

        foreach ($menuEntities as $menu) {
            $menuAccess[$menu->getId()] = [
                'id' => $menu->getId(),
                'title' => $menu->getName(),
                'min_role' => $menu->getMinRole(),
                'items' => [],
                'sub_menus' => [],
                'sortOrder' => $menu->getSortOrder()
            ];

            if ($debugMode) {
                $debugLog[] = [
                    'context' => 'menu: ' . $menu->getName(),
                    'menuId' => $menu->getId(),
                    'minRole' => $menu->getMinRole(),
                    'userRoles' => $userRoles
                ];
            }
        }

        foreach ($subMenuEntities as $sub) {
            $menuId = method_exists($sub, 'getParentId') ? $sub->getParentId() : null;

            $subMenuAccess[$sub->getId()] = [
                'id' => $sub->getId(),
                'title' => $sub->getName(),
                'parentId' => $menuId,
                'min_role' => $sub->getMinRole(),
                'items' => [],
                'sortOrder' => $sub->getSortOrder()
            ];

            if ($debugMode) {
                $debugLog[] = [
                    'context' => 'submenu: ' . $sub->getName(),
                    'subMenuId' => $sub->getId(),
                    'parentId' => $menuId,
                    'minRole' => $sub->getMinRole(),
                    'userRoles' => $userRoles
                ];
            }
        }

        foreach ($menuItems as $item) {
            if (!$this->hasAccess($item->getMinRole(), $userRoles, $debugMode, 'menu_item: ' . $item->getName(), $debugLog)) {
                continue;
            }

            if ($item->getMenuId() && isset($menuAccess[$item->getMenuId()])) {
                $menuAccess[$item->getMenuId()]['items'][] = [
                    'id' => $item->getId(),
                    'title' => $item->getName(),
                    'route' => $item->getRoute(),
                    'sortOrder' => $item->getSortOrder()
                ];
            }

            if ($item->getSubMenuId() && isset($subMenuAccess[$item->getSubMenuId()])) {
                $subMenuAccess[$item->getSubMenuId()]['items'][] = [
                    'id' => $item->getId(),
                    'title' => $item->getName(),
                    'route' => $item->getRoute(),
                    'sortOrder' => $item->getSortOrder()
                ];
            }
            
        }

        // Sort menu items and sub-menu items by sortOrder TestBlock. Leave it in code!
        /*foreach ($menuAccess as &$menu) {
            usort($menu['items'], fn($a, $b) => $a['sortOrder'] <=> $b['sortOrder']);
        }
        foreach ($subMenuAccess as &$sub) {
            usort($sub['items'], fn($a, $b) => $a['sortOrder'] <=> $b['sortOrder']);
        }*/

        foreach ($subMenuAccess as $subId => $sub) {
            $minRole = $sub['min_role'] ?? null;
            $parentId = $sub['parentId'] ?? null;

            if (!$this->hasAccess($minRole, $userRoles, $debugMode, 'submenu: ' . $sub['title'], $debugLog)) {
                continue;
            }

            if ($parentId === null || !isset($menuAccess[$parentId])) {
                if ($debugMode) {
                    $debugLog[] = [
                        'context' => 'submenu skipped (no valid parent)',
                        'subId' => $subId,
                        'title' => $sub['title'],
                        'parentId' => $parentId
                    ];
                }
                continue;
            }

            unset($sub['parentId'], $sub['min_role']);
            $menuAccess[$parentId]['sub_menus'][] = $sub;
        }

        $finalMenus = [];
        $addedIds = [];

        foreach ($menuAccess as $menu) {
            $id = $menu['id'];
            $minRole = $menu['min_role'] ?? null;
            $hasAccessToMenu = $this->hasAccess($minRole, $userRoles, $debugMode, 'menu: ' . $menu['title'], $debugLog);
            $menuIsEmpty = empty($menu['items']) && empty($menu['sub_menus']);

            if ($id === 13 && $debugMode) {
                $debugLog[] = [
                    'context' => '🧪 Test-Menü Analyse',
                    'id' => $menu['id'],
                    'title' => $menu['title'],
                    'itemsCount' => count($menu['items']),
                    'subMenusCount' => count($menu['sub_menus']),
                    'hasAccess' => $hasAccessToMenu,
                    'menuIsEmpty' => $menuIsEmpty,
                    'willBeIncluded' => $hasAccessToMenu && (!$menuIsEmpty || $showEmptyMenus)
                ];
            }

            if (in_array($id, $addedIds)) {
                if ($debugMode) {
                    $debugLog[] = [
                        'context' => '⚠️ Duplicate menu ID detected – skipping',
                        'id' => $id,
                        'title' => $menu['title']
                    ];
                }
                continue;
            }

            if ($hasAccessToMenu && (!$menuIsEmpty || $showEmptyMenus)) {
                unset($menu['min_role']);
                $finalMenus[] = $menu;
                $addedIds[] = $id;

                if ($debugMode) {
                    $debugLog[] = [
                        'context' => '✅ Menu added',
                        'title' => $menu['title'],
                        'id' => $id,
                        'empty' => $menuIsEmpty,
                        'showEmptyMenus' => $showEmptyMenus
                    ];
                }
            } elseif ($debugMode) {
                $debugLog[] = [
                    'context' => '❌ Menu skipped',
                    'title' => $menu['title'],
                    'id' => $id,
                    'hasAccess' => $hasAccessToMenu,
                    'empty' => $menuIsEmpty,
                    'showEmptyMenus' => $showEmptyMenus
                ];
            }
        }
        // ➕ Developer-Menü dynamisch hinzufügen, wenn ROLE_DEVELOPER
        if (in_array('ROLE_DEVELOPER', $userRoles, true)) {
            $developerRoutes = array_filter(
                $this->router->getRouteCollection()->all(),
                fn($route, $name) => $route->getPath() && !str_starts_with($name, '_'), // Exclude routes starting with "_"
                ARRAY_FILTER_USE_BOTH
            );

            $devItems = [];
            foreach ($developerRoutes as $name => $route) {
                // Ensure mandatory parameters are handled
                $routeParams = [];
                foreach ($route->getRequirements() as $param => $requirement) {
                    if ($param === 'token') {
                        $routeParams[$param] = 'sample_token'; // Provide a default token value
                    } else {
                        $routeParams[$param] = $requirement === '\d+' ? 123 : ($requirement === '.*' ? 'default' : $requirement);
                    }
                }

                try {
                    $generatedRoute = $this->router->generate($name, $routeParams);
                } catch (\Exception $e) {
                    if ($debugMode) {
                        $debugLog[] = [
                            'context' => '⚠️ Route generation failed',
                            'routeName' => $name,
                            'error' => $e->getMessage()
                        ];
                    }
                    continue; // Skip routes that fail to generate
                }

                $devItems[] = [
                    'id' => 'dev_' . $name,
                    'title' => $name,
                    'route' => $generatedRoute,
                    'sortOrder' => 0
                ];
            }

            usort($devItems, fn($a, $b) => strcmp($a['title'], $b['title']));

            $finalMenus[] = [
                'id' => 9999,
                'title' => '🛠️ Developer',
                'items' => $devItems,
                'sub_menus' => [],
                'sortOrder' => 9999
            ];
        }

        
        $result = [
            'menus' => array_values($finalMenus),
            'user_roles' => $userRoles
        ];

        if ($debugMode) {
            $result['debug'] = $debugLog;
        }

        return $result;
    }

    public function getMenuForRole(array $roles): array
    {
        return $this->getStructuredMenu(true, $roles);
    }
}