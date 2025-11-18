<?php

namespace App\Controller;

use App\Entity\DashboardModules;
use App\Form\DashboardModulesType;
use App\Repository\DashboardModulesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Service\DashboardModuleRenderer;


#[Route('/admin/dashboard-modules')]
final class DashboardModulesController extends AbstractController
{
    #[Route(name: 'app_dashboard_modules_index', methods: ['GET'])]
    public function index(
        DashboardModulesRepository $dashboardModulesRepository,
        DashboardModuleRenderer $dashboardModuleRenderer
    ): Response {
        $modules = $dashboardModulesRepository->findAll();
    
        $renderedContents = [];
    
        foreach ($modules as $module) {
            $renderedContents[$module->getId()] = $dashboardModuleRenderer->renderPreview($module->getContent() ?? '');
        }
    
        return $this->render('dashboard_modules/index.html.twig', [
            'dashboard_modules' => $modules,
            'rendered_contents' => $renderedContents,
            'page_title' => 'Dashboard Modules',
            'page_description' => 'Manage your dashboard modules here.',
        ]);
    }
    #[Route('/new', name: 'app_dashboard_modules_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $dashboardModule = new DashboardModules();
        $form = $this->createForm(DashboardModulesType::class, $dashboardModule);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($dashboardModule);
            $entityManager->flush();

            return $this->redirectToRoute('app_dashboard_modules_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('dashboard_modules/new.html.twig', [
            'dashboard_module' => $dashboardModule,
            'form' => $form,
            'page_title' => 'Create New Dashboard Module',
            'page_description' => 'Fill in the details to create a new dashboard module.',
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_dashboard_modules_show', methods: ['GET'])]
    public function show(DashboardModules $dashboardModule): Response
    {
        return $this->render('dashboard_modules/show.html.twig', [
            'dashboard_module' => $dashboardModule,
        ]);
    }

    #[Route('/{id<\d+>}/edit', name: 'app_dashboard_modules_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, DashboardModules $dashboardModule, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(DashboardModulesType::class, $dashboardModule);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_dashboard_modules_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('dashboard_modules/edit.html.twig', [
            'dashboard_module' => $dashboardModule,
            'form' => $form,
            'page_title' => 'Edit Dashboard Module',
            'page_description' => 'Modify the details of the selected dashboard module.',
            'is_edit' => true,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_dashboard_modules_delete', methods: ['POST'])]
    public function delete(Request $request, DashboardModules $dashboardModule, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$dashboardModule->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($dashboardModule);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_dashboard_modules_index', [], Response::HTTP_SEE_OTHER);
    }
}
