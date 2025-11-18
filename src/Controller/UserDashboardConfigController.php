<?php

namespace App\Controller;

use App\Entity\DashboardModules;
use App\Entity\UserDashboardConfig;
use App\Form\UserDashboardConfigType;
use App\Repository\DashboardModulesRepository;
use App\Repository\UserDashboardConfigRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/user-dashboard/config')]
final class UserDashboardConfigController extends AbstractController
{
    #[Route(name: 'app_user_dashboard_config_index', methods: ['GET'])]
    public function index(UserDashboardConfigRepository $repo): Response
    {
        return $this->render('user_dashboard_config/index.html.twig', [
            'user_dashboard_configs' => $repo->findAll(),
            'page_title' => 'Dashboard Configurations',
            'page_description' => 'Manage the dashboard configurations for users.',
        ]);
    }

    #[Route('/new', name: 'app_user_dashboard_config_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $config = new UserDashboardConfig();
        $form = $this->createForm(UserDashboardConfigType::class, $config);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($config);
            $em->flush();
            return $this->redirectToRoute('app_user_dashboard_config_index');
        }

        return $this->render('user_dashboard_config/new.html.twig', [
            'form' => $form,
            'page_title' => 'Create New Dashboard Configuration',
            'page_description' => 'Fill in the details to create a new dashboard configuration.',
        ]);
    }

    // === ME (eigener Benutzer) ===

    #[Route('/me', name: 'app_user_dashboard_config_me', methods: ['GET'])]
    public function myDashboardConfig(UserDashboardConfigRepository $repo): Response
    {
        $user = $this->getUser();
        $configs = $repo->findBy(['user' => $user]);

        return $this->render('user_dashboard_config/my.html.twig', [
            'user_dashboard_configs' => $configs,
            'page_title' => 'Mein Dashboard',
            'page_description' => 'Deine persönliche Dashboard-Konfiguration.',
        ]);
    }

    #[Route('/me/add', name: 'app_user_dashboard_config_me_add', methods: ['GET', 'POST'])]
    public function addForMe(Request $request, DashboardModulesRepository $moduleRepo, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        $allModules = $moduleRepo->findAll();

        $config = new UserDashboardConfig();
        $config->setUser($user);

        $form = $this->createForm(UserDashboardConfigType::class, $config, [
            'available_modules' => $allModules,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($config);
            $em->flush();

            $this->addFlash('success', 'Modul zum Dashboard hinzugefügt.');
            return $this->redirectToRoute('app_user_dashboard_config_me');
        }

        return $this->render('user_dashboard_config/add_my.html.twig', [
            'form' => $form,
            'page_title' => 'Modul zum Dashboard hinzufügen',
        ]);
    }

    #[Route('/me/edit/{id<\d+>}', name: 'app_user_dashboard_config_me_edit', methods: ['GET', 'POST'])]
    public function editForMe(Request $request, UserDashboardConfig $config, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        if ($config->getUser() !== $user) {
            throw $this->createAccessDeniedException('Nicht erlaubt.');
        }

        $form = $this->createForm(UserDashboardConfigType::class, $config);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $config->setUpdatedAt(new \DateTime());
            $em->flush();
            $this->addFlash('success', 'Dashboard-Einstellungen gespeichert.');
            return $this->redirectToRoute('app_user_dashboard_config_me');
        }

        return $this->render('user_dashboard_config/edit_my.html.twig', [
            'form' => $form,
            'page_title' => 'Mein Dashboard bearbeiten',
        ]);
    }

    #[Route('/me/delete/{id<\d+>}', name: 'app_user_dashboard_config_me_delete', methods: ['POST'])]
    public function deleteForMe(Request $request, UserDashboardConfig $config, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        if ($config->getUser() !== $user) {
            throw $this->createAccessDeniedException('Nicht erlaubt.');
        }

        $token = $request->getPayload()->getString('_token');
        if ($this->isCsrfTokenValid('delete' . $config->getId(), $token)) {
            $em->remove($config);
            $em->flush();
            $this->addFlash('success', 'Modul gelöscht.');
        }

        return $this->redirectToRoute('app_user_dashboard_config_me');
    }

    #[Route('/me/sort', name: 'app_user_dashboard_config_sort', methods: ['POST'])]
public function sort(Request $request, UserDashboardConfigRepository $repo, EntityManagerInterface $em): Response
{
    $user = $this->getUser();
    $data = json_decode($request->getContent(), true);
    $ids = $data['ids'] ?? [];

    if (!is_array($ids)) {
        return $this->json(['status' => 'error', 'error' => 'Invalid data'], 400);
    }

    $configs = $repo->findBy(['user' => $user]);

    // ID => Entity-Map für schnelle Zuordnung
    $configMap = [];
    foreach ($configs as $conf) {
        $configMap[$conf->getId()] = $conf;
    }

    foreach ($ids as $index => $id) {
        if (isset($configMap[$id])) {
            $configMap[$id]->setSortOrder($index);
        }
    }

    $em->flush();

    return $this->json(['status' => 'ok']);
}


    // === Admin/Global ===

    #[Route('/{id<\d+>}', name: 'app_user_dashboard_config_show', methods: ['GET'])]
    public function show(UserDashboardConfig $config): Response
    {
        return $this->render('user_dashboard_config/show.html.twig', [
            'user_dashboard_config' => $config,
        ]);
    }

    #[Route('/{id<\d+>}/edit', name: 'app_user_dashboard_config_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, UserDashboardConfig $config, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(UserDashboardConfigType::class, $config);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            return $this->redirectToRoute('app_user_dashboard_config_index');
        }

        return $this->render('user_dashboard_config/edit.html.twig', [
            'form' => $form,
            'page_title' => 'Edit Dashboard Configuration',
            'page_description' => 'Modify the details of the selected dashboard configuration.',
        ]);
    }

    #[Route('/{id<\d+>}/delete', name: 'app_user_dashboard_config_delete', methods: ['POST'])]
    public function delete(Request $request, UserDashboardConfig $config, EntityManagerInterface $em): Response
    {
        $token = $request->getPayload()->getString('_token');
        if ($this->isCsrfTokenValid('delete' . $config->getId(), $token)) {
            $em->remove($config);
            $em->flush();
        }

        return $this->redirectToRoute('app_user_dashboard_config_index');
    }
}
