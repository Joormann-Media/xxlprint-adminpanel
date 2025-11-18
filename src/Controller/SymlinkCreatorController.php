<?php

namespace App\Controller;

use App\Entity\SymlinkCreator;
use App\Form\SymlinkCreatorForm;
use App\Repository\SymlinkCreatorRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/symlink/creator')]
final class SymlinkCreatorController extends AbstractController
{
    #[Route(name: 'app_symlink_creator_index', methods: ['GET'])]
    public function index(SymlinkCreatorRepository $symlinkCreatorRepository): Response
    {
        return $this->render('symlink_creator/index.html.twig', [
            'symlink_creators' => $symlinkCreatorRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_symlink_creator_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $symlinkCreator = new SymlinkCreator();
        $form = $this->createForm(SymlinkCreatorForm::class, $symlinkCreator);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($symlinkCreator);
            $entityManager->flush();

            return $this->redirectToRoute('app_symlink_creator_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('symlink_creator/new.html.twig', [
            'symlink_creator' => $symlinkCreator,
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_symlink_creator_show', methods: ['GET'])]
    public function show(SymlinkCreator $symlinkCreator): Response
    {
        return $this->render('symlink_creator/show.html.twig', [
            'symlink_creator' => $symlinkCreator,
        ]);
    }

    #[Route('/{id<\d+>}/edit', name: 'app_symlink_creator_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, SymlinkCreator $symlinkCreator, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(SymlinkCreatorForm::class, $symlinkCreator);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_symlink_creator_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('symlink_creator/edit.html.twig', [
            'symlink_creator' => $symlinkCreator,
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_symlink_creator_delete', methods: ['POST'])]
    public function delete(Request $request, SymlinkCreator $symlinkCreator, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$symlinkCreator->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($symlinkCreator);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_symlink_creator_index', [], Response::HTTP_SEE_OTHER);
    }
}
