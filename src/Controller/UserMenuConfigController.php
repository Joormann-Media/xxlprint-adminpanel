<?php

namespace App\Controller;

use App\Entity\UserMenuConfig;
use App\Form\UserMenuConfigForm;
use App\Repository\UserMenuConfigRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/user/menu/config')]
final class UserMenuConfigController extends AbstractController
{
    #[Route(name: 'app_user_menu_config_index', methods: ['GET'])]
    public function index(UserMenuConfigRepository $userMenuConfigRepository): Response
    {
        return $this->render('user_menu_config/index.html.twig', [
            'user_menu_configs' => $userMenuConfigRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_user_menu_config_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $userMenuConfig = new UserMenuConfig();
        $form = $this->createForm(UserMenuConfigForm::class, $userMenuConfig);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($userMenuConfig);
            $entityManager->flush();

            return $this->redirectToRoute('app_user_menu_config_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user_menu_config/new.html.twig', [
            'user_menu_config' => $userMenuConfig,
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_user_menu_config_show', methods: ['GET'])]
    public function show(UserMenuConfig $userMenuConfig): Response
    {
        return $this->render('user_menu_config/show.html.twig', [
            'user_menu_config' => $userMenuConfig,
        ]);
    }

    #[Route('/{id<\d+>}/edit', name: 'app_user_menu_config_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, UserMenuConfig $userMenuConfig, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(UserMenuConfigForm::class, $userMenuConfig);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_user_menu_config_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user_menu_config/edit.html.twig', [
            'user_menu_config' => $userMenuConfig,
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_user_menu_config_delete', methods: ['POST'])]
    public function delete(Request $request, UserMenuConfig $userMenuConfig, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$userMenuConfig->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($userMenuConfig);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_user_menu_config_index', [], Response::HTTP_SEE_OTHER);
    }
}
