<?php

namespace App\Controller;

use App\Entity\HelpEntry;
use App\Form\HelpEntryType;
use App\Repository\HelpEntryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/help-entry')]
final class HelpEntryController extends AbstractController
{
    #[Route(name: 'admin_app_help_entry_index', methods: ['GET'])]
    public function index(HelpEntryRepository $helpEntryRepository): Response
    {
        return $this->render('admin/help_entry/index.html.twig', [
            'help_entries' => $helpEntryRepository->findAll(),
            'page_title' => 'Hilfeeinträge',
            'page_description' => 'Hier können Sie alle Hilfeeinträge verwalten.',
        ]);
    }

    #[Route('/new', name: 'admin_app_help_entry_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $helpEntry = new HelpEntry();
        $form = $this->createForm(HelpEntryType::class, $helpEntry);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($helpEntry);
            $entityManager->flush();

            return $this->redirectToRoute('admin_app_help_entry_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/help_entry/new.html.twig', [
            'help_entry' => $helpEntry,
            'form' => $form,
            'page_title' => 'Neuer Hilfeeintrag',
            'page_description' => 'Hier können Sie einen neuen Hilfeeintrag erstellen.',
        ]);
    }

    #[Route('/{id<\d+>}', name: 'admin_app_help_entry_show', methods: ['GET'])]
    public function show(HelpEntry $helpEntry): Response
    {
        return $this->render('admin/help_entry/show.html.twig', [
            'help_entry' => $helpEntry,
            'page_title' => 'Hilfeeintrag anzeigen',
            'page_description' => 'Hier können Sie die Details des Hilfeeintrags anzeigen.',
        ]);
    }

    #[Route('/{id<\d+>}/edit', name: 'admin_app_help_entry_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, HelpEntry $helpEntry, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(HelpEntryType::class, $helpEntry);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('admin_app_help_entry_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/help_entry/edit.html.twig', [
            'help_entry' => $helpEntry,
            'form' => $form,
            'page_title' => 'Hilfeeintrag bearbeiten',
            'page_description' => 'Hier können Sie den Hilfeeintrag bearbeiten.',
        ]);
    }

    #[Route('/{id<\d+>}', name: 'admin_app_help_entry_delete', methods: ['POST'])]
    public function delete(Request $request, HelpEntry $helpEntry, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$helpEntry->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($helpEntry);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_help_entry_index', [], Response::HTTP_SEE_OTHER);
    }
}
