<?php

namespace App\Controller;

use App\Entity\ReleaseFile;
use App\Form\ReleaseFileForm;
use App\Repository\ReleaseFileRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/release/file')]
final class ReleaseFileController extends AbstractController
{
    #[Route(name: 'app_release_file_index', methods: ['GET'])]
    public function index(ReleaseFileRepository $releaseFileRepository): Response
    {
        return $this->render('release_file/index.html.twig', [
            'release_files' => $releaseFileRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_release_file_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $releaseFile = new ReleaseFile();
        $form = $this->createForm(ReleaseFileForm::class, $releaseFile);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($releaseFile);
            $entityManager->flush();

            return $this->redirectToRoute('app_release_file_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('release_file/new.html.twig', [
            'release_file' => $releaseFile,
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_release_file_show', methods: ['GET'])]
    public function show(ReleaseFile $releaseFile): Response
    {
        return $this->render('release_file/show.html.twig', [
            'release_file' => $releaseFile,
        ]);
    }

    #[Route('/{id<\d+>}/edit', name: 'app_release_file_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ReleaseFile $releaseFile, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ReleaseFileForm::class, $releaseFile);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_release_file_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('release_file/edit.html.twig', [
            'release_file' => $releaseFile,
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_release_file_delete', methods: ['POST'])]
    public function delete(Request $request, ReleaseFile $releaseFile, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$releaseFile->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($releaseFile);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_release_file_index', [], Response::HTTP_SEE_OTHER);
    }
}
