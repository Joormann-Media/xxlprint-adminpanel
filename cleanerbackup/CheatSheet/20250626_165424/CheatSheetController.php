<?php

namespace App\Controller;

use App\Entity\CheatSheet;
use App\Form\CheatSheetForm;
use App\Repository\CheatSheetRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/cheat/sheet')]
final class CheatSheetController extends AbstractController
{
    #[Route(name: 'app_cheat_sheet_index', methods: ['GET'])]
    public function index(CheatSheetRepository $cheatSheetRepository): Response
    {
        return $this->render('cheat_sheet/index.html.twig', [
            'cheat_sheets' => $cheatSheetRepository->findAll(),
            'page_title' => 'Cheat Sheets - Übersicht',
        ]);
    }

    #[Route('/new', name: 'app_cheat_sheet_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $cheatSheet = new CheatSheet();
        $form = $this->createForm(CheatSheetForm::class, $cheatSheet);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($cheatSheet);
            $entityManager->flush();

            return $this->redirectToRoute('app_cheat_sheet_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('cheat_sheet/new.html.twig', [
            'cheat_sheet' => $cheatSheet,
            'form' => $form,
            'page_title' => 'Cheat Sheets - Neu erstellen',
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_cheat_sheet_show', methods: ['GET'])]
    public function show(CheatSheet $cheatSheet): Response
    {
        return $this->render('cheat_sheet/show.html.twig', [
            'cheat_sheet' => $cheatSheet,
            'page_title' => 'Cheat Sheet - ' . $cheatSheet->getName(),
        ]);
    }

    #[Route('/{id<\d+>}/edit', name: 'app_cheat_sheet_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, CheatSheet $cheatSheet, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CheatSheetForm::class, $cheatSheet);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_cheat_sheet_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('cheat_sheet/edit.html.twig', [
            'cheat_sheet' => $cheatSheet,
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_cheat_sheet_delete', methods: ['POST'])]
    public function delete(Request $request, CheatSheet $cheatSheet, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$cheatSheet->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($cheatSheet);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_cheat_sheet_index', [], Response::HTTP_SEE_OTHER);
    }
}
