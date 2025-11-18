<?php

namespace App\Controller;

use App\Entity\SystemConfig;
use App\Form\SystemConfigType;
use App\Repository\SystemConfigRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/system/config')]
final class SystemConfigController extends AbstractController
{
    #[Route(name: 'app_system_config_index', methods: ['GET'])]
    public function index(SystemConfigRepository $systemConfigRepository): Response
    {
        return $this->render('system_config/index.html.twig', [
            'system_configs' => $systemConfigRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_system_config_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $systemConfig = new SystemConfig();
        $form = $this->createForm(SystemConfigType::class, $systemConfig);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($systemConfig);
            $entityManager->flush();

            return $this->redirectToRoute('app_system_config_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('system_config/new.html.twig', [
            'system_config' => $systemConfig,
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_system_config_show', methods: ['GET'])]
    public function show(SystemConfig $systemConfig): Response
    {
        return $this->render('system_config/show.html.twig', [
            'system_config' => $systemConfig,
        ]);
    }

    #[Route('/{id<\d+>}/edit', name: 'app_system_config_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, SystemConfig $systemConfig, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(SystemConfigType::class, $systemConfig);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_system_config_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('system_config/edit.html.twig', [
            'system_config' => $systemConfig,
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_system_config_delete', methods: ['POST'])]
    public function delete(Request $request, SystemConfig $systemConfig, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$systemConfig->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($systemConfig);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_system_config_index', [], Response::HTTP_SEE_OTHER);
    }
}
