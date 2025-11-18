<?php

namespace App\Controller;

use App\Entity\TranslationKey;
use App\Form\TranslationKeyType;
use App\Repository\TranslationKeyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/translation/key')]
final class TranslationKeyController extends AbstractController
{
    #[Route(name: 'app_translation_key_index', methods: ['GET'])]
    public function index(TranslationKeyRepository $translationKeyRepository): Response
    {
        return $this->render('translation_key/index.html.twig', [
            'translation_keys' => $translationKeyRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_translation_key_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $translationKey = new TranslationKey();
        $form = $this->createForm(TranslationKeyType::class, $translationKey);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($translationKey);
            $entityManager->flush();

            return $this->redirectToRoute('app_translation_key_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('translation_key/new.html.twig', [
            'translation_key' => $translationKey,
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_translation_key_show', methods: ['GET'])]
    public function show(TranslationKey $translationKey): Response
    {
        return $this->render('translation_key/show.html.twig', [
            'translation_key' => $translationKey,
        ]);
    }

    #[Route('/{id<\d+>}/edit', name: 'app_translation_key_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, TranslationKey $translationKey, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(TranslationKeyType::class, $translationKey);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_translation_key_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('translation_key/edit.html.twig', [
            'translation_key' => $translationKey,
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_translation_key_delete', methods: ['POST'])]
    public function delete(Request $request, TranslationKey $translationKey, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$translationKey->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($translationKey);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_translation_key_index', [], Response::HTTP_SEE_OTHER);
    }
}
