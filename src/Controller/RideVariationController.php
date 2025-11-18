<?php

namespace App\Controller;

use App\Entity\RideVariation;
use App\Form\RideVariationType;
use App\Repository\RideVariationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/ride/variation')]
final class RideVariationController extends AbstractController
{
    #[Route(name: 'app_ride_variation_index', methods: ['GET'])]
    public function index(RideVariationRepository $rideVariationRepository): Response
    {
        return $this->render('ride_variation/index.html.twig', [
            'ride_variations' => $rideVariationRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_ride_variation_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $rideVariation = new RideVariation();
        $form = $this->createForm(RideVariationType::class, $rideVariation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($rideVariation);
            $entityManager->flush();

            return $this->redirectToRoute('app_ride_variation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('ride_variation/new.html.twig', [
            'ride_variation' => $rideVariation,
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_ride_variation_show', methods: ['GET'])]
    public function show(RideVariation $rideVariation): Response
    {
        return $this->render('ride_variation/show.html.twig', [
            'ride_variation' => $rideVariation,
        ]);
    }

    #[Route('/{id<\d+>}/edit', name: 'app_ride_variation_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, RideVariation $rideVariation, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(RideVariationType::class, $rideVariation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_ride_variation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('ride_variation/edit.html.twig', [
            'ride_variation' => $rideVariation,
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_ride_variation_delete', methods: ['POST'])]
    public function delete(Request $request, RideVariation $rideVariation, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$rideVariation->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($rideVariation);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_ride_variation_index', [], Response::HTTP_SEE_OTHER);
    }
}
