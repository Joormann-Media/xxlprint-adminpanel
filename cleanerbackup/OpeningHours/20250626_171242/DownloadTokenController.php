<?php

namespace App\Controller;

use App\Entity\DownloadToken;
use App\Form\DownloadTokenForm;
use App\Repository\DownloadTokenRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/download/token')]
final class DownloadTokenController extends AbstractController
{
    #[Route(name: 'app_download_token_index', methods: ['GET'])]
    public function index(DownloadTokenRepository $downloadTokenRepository): Response
    {
        return $this->render('download_token/index.html.twig', [
            'download_tokens' => $downloadTokenRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_download_token_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $downloadToken = new DownloadToken();
        $form = $this->createForm(DownloadTokenForm::class, $downloadToken);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($downloadToken);
            $entityManager->flush();

            return $this->redirectToRoute('app_download_token_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('download_token/new.html.twig', [
            'download_token' => $downloadToken,
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_download_token_show', methods: ['GET'])]
    public function show(DownloadToken $downloadToken): Response
    {
        return $this->render('download_token/show.html.twig', [
            'download_token' => $downloadToken,
        ]);
    }

    #[Route('/{id<\d+>}/edit', name: 'app_download_token_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, DownloadToken $downloadToken, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(DownloadTokenForm::class, $downloadToken);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_download_token_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('download_token/edit.html.twig', [
            'download_token' => $downloadToken,
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_download_token_delete', methods: ['POST'])]
    public function delete(Request $request, DownloadToken $downloadToken, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$downloadToken->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($downloadToken);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_download_token_index', [], Response::HTTP_SEE_OTHER);
    }
}
