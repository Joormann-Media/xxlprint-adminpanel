<?php

namespace App\Controller;

use App\Entity\OnlineStatusConfig;
use App\Form\OnlineStatusConfigType;
use App\Repository\OnlineStatusConfigRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/online-status/config')]
final class OnlineStatusConfigController extends AbstractController
{
    #[Route(name: 'app_online_status_config_index', methods: ['GET'])]
    public function index(OnlineStatusConfigRepository $onlineStatusConfigRepository): Response
    {
        return $this->render('online_status_config/index.html.twig', [
            'online_status_configs' => $onlineStatusConfigRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_online_status_config_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $onlineStatusConfig = new OnlineStatusConfig();
        $form = $this->createForm(OnlineStatusConfigType::class, $onlineStatusConfig);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($onlineStatusConfig);
            $entityManager->flush();

            return $this->redirectToRoute('app_online_status_config_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('online_status_config/new.html.twig', [
            'online_status_config' => $onlineStatusConfig,
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_online_status_config_show', methods: ['GET'])]
    public function show(OnlineStatusConfig $onlineStatusConfig): Response
    {
        return $this->render('online_status_config/show.html.twig', [
            'online_status_config' => $onlineStatusConfig,
        ]);
    }

    #[Route('/{id<\d+>}/edit', name: 'app_online_status_config_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, OnlineStatusConfig $onlineStatusConfig, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(OnlineStatusConfigType::class, $onlineStatusConfig);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_online_status_config_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('online_status_config/edit.html.twig', [
            'online_status_config' => $onlineStatusConfig,
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_online_status_config_delete', methods: ['POST'])]
    public function delete(Request $request, OnlineStatusConfig $onlineStatusConfig, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$onlineStatusConfig->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($onlineStatusConfig);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_online_status_config_index', [], Response::HTTP_SEE_OTHER);
    }
}
