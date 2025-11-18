<?php

namespace App\Controller;

use App\Entity\GpsPosition;
use App\Form\GpsPositionType;
use App\Repository\GpsPositionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/gps/position')]
final class GpsPositionController extends AbstractController
{
    #[Route(name: 'app_gps_position_index', methods: ['GET'])]
    public function index(GpsPositionRepository $gpsPositionRepository): Response
    {
        return $this->render('gps_position/index.html.twig', [
            'gps_positions' => $gpsPositionRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_gps_position_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $gpsPosition = new GpsPosition();
        $form = $this->createForm(GpsPositionType::class, $gpsPosition);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($gpsPosition);
            $entityManager->flush();

            return $this->redirectToRoute('app_gps_position_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('gps_position/new.html.twig', [
            'gps_position' => $gpsPosition,
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_gps_position_show', methods: ['GET'])]
    public function show(GpsPosition $gpsPosition): Response
    {
        return $this->render('gps_position/show.html.twig', [
            'gps_position' => $gpsPosition,
        ]);
    }

    #[Route('/{id<\d+>}/edit', name: 'app_gps_position_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, GpsPosition $gpsPosition, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(GpsPositionType::class, $gpsPosition);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_gps_position_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('gps_position/edit.html.twig', [
            'gps_position' => $gpsPosition,
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_gps_position_delete', methods: ['POST'])]
    public function delete(Request $request, GpsPosition $gpsPosition, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$gpsPosition->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($gpsPosition);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_gps_position_index', [], Response::HTTP_SEE_OTHER);
    }
}
