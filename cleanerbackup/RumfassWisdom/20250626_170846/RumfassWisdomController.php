<?php

namespace App\Controller;

use App\Entity\RumfassWisdom;
use App\Form\RumfassWisdomForm;
use App\Repository\RumfassWisdomRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/rumfass/wisdom')]
final class RumfassWisdomController extends AbstractController
{
    #[Route(name: 'app_rumfass_wisdom_index', methods: ['GET'])]
    public function index(RumfassWisdomRepository $rumfassWisdomRepository): Response
    {
        return $this->render('rumfass_wisdom/index.html.twig', [
            'rumfass_wisdoms' => $rumfassWisdomRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_rumfass_wisdom_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $rumfassWisdom = new RumfassWisdom();
        $form = $this->createForm(RumfassWisdomForm::class, $rumfassWisdom);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($rumfassWisdom);
            $entityManager->flush();

            return $this->redirectToRoute('app_rumfass_wisdom_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('rumfass_wisdom/new.html.twig', [
            'rumfass_wisdom' => $rumfassWisdom,
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_rumfass_wisdom_show', methods: ['GET'])]
    public function show(RumfassWisdom $rumfassWisdom): Response
    {
        return $this->render('rumfass_wisdom/show.html.twig', [
            'rumfass_wisdom' => $rumfassWisdom,
        ]);
    }

    #[Route('/{id<\d+>}/edit', name: 'app_rumfass_wisdom_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, RumfassWisdom $rumfassWisdom, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(RumfassWisdomForm::class, $rumfassWisdom);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_rumfass_wisdom_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('rumfass_wisdom/edit.html.twig', [
            'rumfass_wisdom' => $rumfassWisdom,
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_rumfass_wisdom_delete', methods: ['POST'])]
    public function delete(Request $request, RumfassWisdom $rumfassWisdom, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$rumfassWisdom->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($rumfassWisdom);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_rumfass_wisdom_index', [], Response::HTTP_SEE_OTHER);
    }
}
