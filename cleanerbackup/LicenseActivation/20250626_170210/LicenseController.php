<?php

namespace App\Controller;

use App\Entity\License;
use App\Form\LicenseType;
use App\Repository\LicenseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/license')]
final class LicenseController extends AbstractController
{
    #[Route(name: 'app_license_index', methods: ['GET'])]
    public function index(LicenseRepository $licenseRepository): Response
    {
        return $this->render('license/index.html.twig', [
            'licenses' => $licenseRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_license_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $license = new License();
        $form = $this->createForm(LicenseType::class, $license);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($license);
            $entityManager->flush();

            return $this->redirectToRoute('app_license_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('license/new.html.twig', [
            'license' => $license,
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_license_show', methods: ['GET'])]
    public function show(License $license): Response
    {
        return $this->render('license/show.html.twig', [
            'license' => $license,
        ]);
    }

    #[Route('/{id<\d+>}/edit', name: 'app_license_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, License $license, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(LicenseType::class, $license);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_license_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('license/edit.html.twig', [
            'license' => $license,
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_license_delete', methods: ['POST'])]
    public function delete(Request $request, License $license, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$license->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($license);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_license_index', [], Response::HTTP_SEE_OTHER);
    }
}
