<?php

namespace App\Controller;

use App\Entity\ModuleBreadcrumb;
use App\Form\ModuleBreadcrumbForm;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/module/breadcrumb')]
final class ModuleBreadcrumbController extends AbstractController
{
    #[Route(name: 'app_module_breadcrumb_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $moduleBreadcrumbs = $entityManager
            ->getRepository(ModuleBreadcrumb::class)
            ->findAll();

        return $this->render('module_breadcrumb/index.html.twig', [
            'module_breadcrumbs' => $moduleBreadcrumbs,
        ]);
    }

    #[Route('/new', name: 'app_module_breadcrumb_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $moduleBreadcrumb = new ModuleBreadcrumb();
        $form = $this->createForm(ModuleBreadcrumbForm::class, $moduleBreadcrumb);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($moduleBreadcrumb);
            $entityManager->flush();

            return $this->redirectToRoute('app_module_breadcrumb_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('module_breadcrumb/new.html.twig', [
            'module_breadcrumb' => $moduleBreadcrumb,
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_module_breadcrumb_show', methods: ['GET'])]
    public function show(ModuleBreadcrumb $moduleBreadcrumb): Response
    {
        return $this->render('module_breadcrumb/show.html.twig', [
            'module_breadcrumb' => $moduleBreadcrumb,
        ]);
    }

    #[Route('/{id<\d+>}/edit', name: 'app_module_breadcrumb_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ModuleBreadcrumb $moduleBreadcrumb, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ModuleBreadcrumbForm::class, $moduleBreadcrumb);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_module_breadcrumb_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('module_breadcrumb/edit.html.twig', [
            'module_breadcrumb' => $moduleBreadcrumb,
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_module_breadcrumb_delete', methods: ['POST'])]
    public function delete(Request $request, ModuleBreadcrumb $moduleBreadcrumb, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$moduleBreadcrumb->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($moduleBreadcrumb);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_module_breadcrumb_index', [], Response::HTTP_SEE_OTHER);
    }
}
