<?php

namespace App\Controller;

use App\Entity\LanguageFile;
use App\Form\LanguageFileType;
use App\Repository\LanguageFileRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/language/file')]
final class LanguageFileController extends AbstractController
{
    #[Route(name: 'app_language_file_index', methods: ['GET'])]
    public function index(LanguageFileRepository $languageFileRepository): Response
    {
        return $this->render('language_file/index.html.twig', [
            'language_files' => $languageFileRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_language_file_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $languageFile = new LanguageFile();
        $form = $this->createForm(LanguageFileType::class, $languageFile);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($languageFile);
            $entityManager->flush();

            return $this->redirectToRoute('app_language_file_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('language_file/new.html.twig', [
            'language_file' => $languageFile,
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_language_file_show', methods: ['GET'])]
    public function show(LanguageFile $languageFile): Response
    {
        return $this->render('language_file/show.html.twig', [
            'language_file' => $languageFile,
        ]);
    }

    #[Route('/{id<\d+>}/edit', name: 'app_language_file_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, LanguageFile $languageFile, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(LanguageFileType::class, $languageFile);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_language_file_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('language_file/edit.html.twig', [
            'language_file' => $languageFile,
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_language_file_delete', methods: ['POST'])]
    public function delete(Request $request, LanguageFile $languageFile, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$languageFile->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($languageFile);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_language_file_index', [], Response::HTTP_SEE_OTHER);
    }
}
