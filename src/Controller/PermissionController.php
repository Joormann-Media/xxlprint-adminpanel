<?php

namespace App\Controller;

use App\Entity\Permission;
use App\Form\PermissionType;
use App\Repository\PermissionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\RouterInterface;

#[Route('/admin/permission')]
final class PermissionController extends AbstractController
{
    #[Route(name: 'app_permission_index', methods: ['GET'])]
    public function index(PermissionRepository $permissionRepository, RouterInterface $router): Response
    {
        $routes = $this->getFilteredRoutes($router);

        return $this->render('permission/index.html.twig', [
            'permissions' => $permissionRepository->findAll(),
            'routes' => $routes,
            'page_title' => 'Permissions - Index',
        ]);
    }

    #[Route('/new', name: 'app_permission_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, RouterInterface $router): Response
    {
        $permission = new Permission();
        $permission->setCreatedate(new \DateTime());
        
        $routes = $this->getFilteredRoutes($router); // 🟢 Hier wird $routes definiert!

        $form = $this->createForm(PermissionType::class, $permission, [
            'routes' => $routes, // 🟢 Hier werden die Routen mitgegeben
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $permission->setCreateBy($this->getUser()->getUsername());

            $entityManager->persist($permission);
            $entityManager->flush();

            return $this->redirectToRoute('app_permission_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('permission/new.html.twig', [
            'permission' => $permission,
            'form' => $form,
            'routes' => $routes, // 🟢 Jetzt ist $routes vorhanden!
            'page_title' => 'Permissions - New',
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_permission_show', methods: ['GET'])]
    public function show(Permission $permission): Response
    {
        return $this->render('permission/show.html.twig', [
            'permission' => $permission,
        ]);
    }

    #[Route('/{id<\d+>}/edit', name: 'app_permission_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Permission $permission, EntityManagerInterface $entityManager, RouterInterface $router): Response
    {
        

        $form = $this->createForm(PermissionType::class, $permission, [
       
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_permission_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('permission/edit.html.twig', [
            'permission' => $permission,
            'form' => $form,
            'page_title' => 'Permissions - Edit',

        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_permission_delete', methods: ['POST'])]
    public function delete(Request $request, Permission $permission, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $permission->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($permission);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_permission_index', [], Response::HTTP_SEE_OTHER);
    }
    
    #[Route('/permission/bulk-delete', name: 'app_permission_bulk_delete', methods: ['POST'])]
    public function bulkDelete(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Holt die IDs als Array (Fehler behoben)
        $permissionIds = $request->request->all('permissions');

        if (!empty($permissionIds) && is_array($permissionIds)) {
            $permissions = $entityManager->getRepository(Permission::class)->findBy(['id' => $permissionIds]);

            foreach ($permissions as $permission) {
                $entityManager->remove($permission);
            }

            $entityManager->flush();
            $this->addFlash('success', 'Ausgewählte Berechtigungen wurden gelöscht.');
        } else {
            $this->addFlash('warning', 'Keine Berechtigungen ausgewählt.');
        }

        return $this->redirectToRoute('app_permission_index');
    }

    /**
     * Erfasst und filtert alle Symfony-Routen.
     */
    private function getAllRoutes(RouterInterface $router): array
    {
        $allRoutes = $router->getRouteCollection()->all();
        $filteredRoutes = [];

        foreach ($allRoutes as $name => $route) {
            $path = $route->getPath();

            // Nur Routen mit Pfaden (kein Debugging-Kram)
            if ($path && $path !== '/') {
                $parts = explode('/', trim($path, '/'));
                $mainRoute = '/' . ($parts[0] ?? '');

                // Haupt-Route speichern
                if (!isset($filteredRoutes[$mainRoute])) {
                    $filteredRoutes[$mainRoute] = [
                        'name' => $name, // Symfony-interner Name der Haupt-Route
                        'path' => $mainRoute,
                        'subroutes' => [],
                    ];
                }

                // Subroute hinzufügen (nur wenn sie nicht schon drin ist)
                if ($path !== $mainRoute) {
                    $filteredRoutes[$mainRoute]['subroutes'][] = [
                        'name' => $name, // Symfony-interner Name der Subroute
                        'path' => $path
                    ];
                }
            }
        }

        ksort($filteredRoutes); // Alphabetische Sortierung nach Hauptrouten
        return $filteredRoutes;
    }

    /**
     * Filters routes for the PermissionController.
     */
    private function getFilteredRoutes(RouterInterface $router): array
    {
        $allRoutes = $router->getRouteCollection()->all();
        $filteredRoutes = [];

        foreach ($allRoutes as $name => $route) {
            $path = $route->getPath();

            // Only include routes with paths (no debugging stuff)
            if ($path && $path !== '/') {
                $parts = explode('/', trim($path, '/'));
                $mainRoute = '/' . ($parts[0] ?? '');

                // Store main route
                if (!isset($filteredRoutes[$mainRoute])) {
                    $filteredRoutes[$mainRoute] = [
                        'name' => $name, // Symfony internal name of the main route
                        'path' => $mainRoute,
                        'subroutes' => [],
                    ];
                }

                // Add subroute (only if it's not already included)
                if ($path !== $mainRoute) {
                    $filteredRoutes[$mainRoute]['subroutes'][] = [
                        'name' => $name, // Symfony internal name of the subroute
                        'path' => $path
                    ];
                }
            }
        }

        ksort($filteredRoutes); // Alphabetical sorting by main routes
        return $filteredRoutes;
    }

}
