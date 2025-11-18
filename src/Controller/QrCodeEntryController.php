<?php

namespace App\Controller;

use App\Entity\QrCodeEntry;
use App\Form\QrCodeEntryForm;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/qr/code/entry')]
final class QrCodeEntryController extends AbstractController
{
    #[Route(name: 'app_qr_code_entry_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $qrCodeEntries = $entityManager
            ->getRepository(QrCodeEntry::class)
            ->findAll();

        return $this->render('qr_code_entry/index.html.twig', [
            'qr_code_entries' => $qrCodeEntries,
        ]);
    }

    #[Route('/new', name: 'app_qr_code_entry_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $qrCodeEntry = new QrCodeEntry();
        $form = $this->createForm(QrCodeEntryForm::class, $qrCodeEntry);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($qrCodeEntry);
            $entityManager->flush();

            return $this->redirectToRoute('app_qr_code_entry_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('qr_code_entry/new.html.twig', [
            'qr_code_entry' => $qrCodeEntry,
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_qr_code_entry_show', methods: ['GET'])]
    public function show(QrCodeEntry $qrCodeEntry): Response
    {
        return $this->render('qr_code_entry/show.html.twig', [
            'qr_code_entry' => $qrCodeEntry,
        ]);
    }

    #[Route('/{id<\d+>}/edit', name: 'app_qr_code_entry_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, QrCodeEntry $qrCodeEntry, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(QrCodeEntryForm::class, $qrCodeEntry);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_qr_code_entry_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('qr_code_entry/edit.html.twig', [
            'qr_code_entry' => $qrCodeEntry,
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_qr_code_entry_delete', methods: ['POST'])]
    public function delete(Request $request, QrCodeEntry $qrCodeEntry, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$qrCodeEntry->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($qrCodeEntry);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_qr_code_entry_index', [], Response::HTTP_SEE_OTHER);
    }
}
