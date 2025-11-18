<?php

namespace App\Controller;

use App\Entity\PasteSystem;
use App\Form\PasteSystemForm;
use App\Repository\PasteSystemRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/paste-system')]
final class PasteSystemController extends AbstractController
{
    #[Route(name: 'app_paste_system_index', methods: ['GET'])]
    public function index(PasteSystemRepository $pasteSystemRepository): Response
    {
        return $this->render('paste_system/index.html.twig', [
            'paste_systems' => $pasteSystemRepository->findAll(),
            'page_title' => 'Paste-System Index',
        ]);
    }

    #[Route('/new', name: 'app_paste_system_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $pasteSystem = new PasteSystem();
        $form = $this->createForm(PasteSystemForm::class, $pasteSystem);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($pasteSystem);
            $entityManager->flush();

            return $this->redirectToRoute('app_paste_system_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('paste_system/new.html.twig', [
            'paste_system' => $pasteSystem,
            'form' => $form,
            'page_title' => 'Paste-System - Create New Paste',
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_paste_system_show', methods: ['GET'])]
    public function show(PasteSystem $pasteSystem): Response
    {
        return $this->render('paste_system/show.html.twig', [
            'paste_system' => $pasteSystem,
            'page_title' => 'Paste-System - Details',
        ]);
    }

    #[Route('/{id<\d+>}/edit', name: 'app_paste_system_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, PasteSystem $pasteSystem, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PasteSystemForm::class, $pasteSystem);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_paste_system_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('paste_system/edit.html.twig', [
            'paste_system' => $pasteSystem,
            'form' => $form,
            'page_title' => 'Paste-System - Edit Paste',
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_paste_system_delete', methods: ['POST'])]
    public function delete(Request $request, PasteSystem $pasteSystem, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$pasteSystem->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($pasteSystem);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_paste_system_index', [], Response::HTTP_SEE_OTHER);
    }
}
