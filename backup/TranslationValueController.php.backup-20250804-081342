<?php

namespace App\Controller;

use App\Entity\TranslationValue;
use App\Form\TranslationValueType;
use App\Repository\TranslationValueRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/translation/value')]
final class TranslationValueController extends AbstractController
{
    #[Route(name: 'app_translation_value_index', methods: ['GET'])]
    public function index(TranslationValueRepository $translationValueRepository): Response
    {
        return $this->render('translation_value/index.html.twig', [
            'translation_values' => $translationValueRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_translation_value_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $translationValue = new TranslationValue();
        $form = $this->createForm(TranslationValueType::class, $translationValue);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($translationValue);
            $entityManager->flush();

            return $this->redirectToRoute('app_translation_value_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('translation_value/new.html.twig', [
            'translation_value' => $translationValue,
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_translation_value_show', methods: ['GET'])]
    public function show(TranslationValue $translationValue): Response
    {
        return $this->render('translation_value/show.html.twig', [
            'translation_value' => $translationValue,
        ]);
    }

    #[Route('/{id<\d+>}/edit', name: 'app_translation_value_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, TranslationValue $translationValue, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(TranslationValueType::class, $translationValue);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_translation_value_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('translation_value/edit.html.twig', [
            'translation_value' => $translationValue,
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_translation_value_delete', methods: ['POST'])]
    public function delete(Request $request, TranslationValue $translationValue, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$translationValue->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($translationValue);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_translation_value_index', [], Response::HTTP_SEE_OTHER);
    }
}
