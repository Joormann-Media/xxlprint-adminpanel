<?php

namespace App\Controller;

use App\Entity\MediaItem;
use App\Form\MediaItemForm;
use App\Repository\MediaItemRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/media/item')]
final class MediaItemController extends AbstractController
{
    #[Route(name: 'app_media_item_index', methods: ['GET'])]
    public function index(MediaItemRepository $mediaItemRepository): Response
    {
        return $this->render('media_item/index.html.twig', [
            'media_items' => $mediaItemRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_media_item_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $mediaItem = new MediaItem();
        $form = $this->createForm(MediaItemForm::class, $mediaItem);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($mediaItem);
            $entityManager->flush();

            return $this->redirectToRoute('app_media_item_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('media_item/new.html.twig', [
            'media_item' => $mediaItem,
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_media_item_show', methods: ['GET'])]
    public function show(MediaItem $mediaItem): Response
    {
        return $this->render('media_item/show.html.twig', [
            'media_item' => $mediaItem,
        ]);
    }

    #[Route('/{id<\d+>}/edit', name: 'app_media_item_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, MediaItem $mediaItem, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(MediaItemForm::class, $mediaItem);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_media_item_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('media_item/edit.html.twig', [
            'media_item' => $mediaItem,
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_media_item_delete', methods: ['POST'])]
    public function delete(Request $request, MediaItem $mediaItem, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$mediaItem->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($mediaItem);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_media_item_index', [], Response::HTTP_SEE_OTHER);
    }
}
