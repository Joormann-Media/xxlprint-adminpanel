<?php

namespace App\Controller;

use App\Entity\AdminConfigModules;
use App\Form\AdminConfigModulesForm;
use App\Repository\AdminConfigModulesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/config/modules')]
final class AdminConfigModulesController extends AbstractController
{
    #[Route(name: 'app_admin_config_modules_index', methods: ['GET'])]
    public function index(AdminConfigModulesRepository $adminConfigModulesRepository): Response
    {
        return $this->render('admin_config_modules/index.html.twig', [
            'admin_config_modules' => $adminConfigModulesRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_admin_config_modules_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $adminConfigModule = new AdminConfigModules();
        $form = $this->createForm(AdminConfigModulesForm::class, $adminConfigModule);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($adminConfigModule);
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_config_modules_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin_config_modules/new.html.twig', [
            'admin_config_module' => $adminConfigModule,
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_admin_config_modules_show', methods: ['GET'])]
    public function show(AdminConfigModules $adminConfigModule): Response
    {
        return $this->render('admin_config_modules/show.html.twig', [
            'admin_config_module' => $adminConfigModule,
        ]);
    }

    #[Route('/{id<\d+>}/edit', name: 'app_admin_config_modules_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, AdminConfigModules $adminConfigModule, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(AdminConfigModulesForm::class, $adminConfigModule);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_config_modules_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin_config_modules/edit.html.twig', [
            'admin_config_module' => $adminConfigModule,
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_admin_config_modules_delete', methods: ['POST'])]
    public function delete(Request $request, AdminConfigModules $adminConfigModule, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$adminConfigModule->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($adminConfigModule);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_admin_config_modules_index', [], Response::HTTP_SEE_OTHER);
    }
}
