<?php

namespace App\Controller;

use App\Entity\LicenseActivation;
use App\Form\LicenseActivationType;
use App\Repository\LicenseActivationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/license/activation')]
final class LicenseActivationController extends AbstractController
{
    #[Route(name: 'app_license_activation_index', methods: ['GET'])]
    public function index(LicenseActivationRepository $licenseActivationRepository): Response
    {
        return $this->render('license_activation/index.html.twig', [
            'license_activations' => $licenseActivationRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_license_activation_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $licenseActivation = new LicenseActivation();
        $form = $this->createForm(LicenseActivationType::class, $licenseActivation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($licenseActivation);
            $entityManager->flush();

            return $this->redirectToRoute('app_license_activation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('license_activation/new.html.twig', [
            'license_activation' => $licenseActivation,
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_license_activation_show', methods: ['GET'])]
    public function show(LicenseActivation $licenseActivation): Response
    {
        return $this->render('license_activation/show.html.twig', [
            'license_activation' => $licenseActivation,
        ]);
    }

    #[Route('/{id<\d+>}/edit', name: 'app_license_activation_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, LicenseActivation $licenseActivation, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(LicenseActivationType::class, $licenseActivation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_license_activation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('license_activation/edit.html.twig', [
            'license_activation' => $licenseActivation,
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_license_activation_delete', methods: ['POST'])]
    public function delete(Request $request, LicenseActivation $licenseActivation, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$licenseActivation->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($licenseActivation);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_license_activation_index', [], Response::HTTP_SEE_OTHER);
    }
}
