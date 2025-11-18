<?php

namespace App\Controller;

use App\Entity\CodingLanguage;
use App\Form\CodingLanguageForm;
use App\Repository\CodingLanguageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/coding/language')]
final class CodingLanguageController extends AbstractController
{
    #[Route(name: 'app_coding_language_index', methods: ['GET'])]
    public function index(CodingLanguageRepository $codingLanguageRepository): Response
    {
        return $this->render('coding_language/index.html.twig', [
            'coding_languages' => $codingLanguageRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_coding_language_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $codingLanguage = new CodingLanguage();
        $form = $this->createForm(CodingLanguageForm::class, $codingLanguage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($codingLanguage);
            $entityManager->flush();

            return $this->redirectToRoute('app_coding_language_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('coding_language/new.html.twig', [
            'coding_language' => $codingLanguage,
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_coding_language_show', methods: ['GET'])]
    public function show(CodingLanguage $codingLanguage): Response
    {
        return $this->render('coding_language/show.html.twig', [
            'coding_language' => $codingLanguage,
        ]);
    }

    #[Route('/{id<\d+>}/edit', name: 'app_coding_language_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, CodingLanguage $codingLanguage, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CodingLanguageForm::class, $codingLanguage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_coding_language_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('coding_language/edit.html.twig', [
            'coding_language' => $codingLanguage,
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_coding_language_delete', methods: ['POST'])]
    public function delete(Request $request, CodingLanguage $codingLanguage, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$codingLanguage->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($codingLanguage);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_coding_language_index', [], Response::HTTP_SEE_OTHER);
    }
}
