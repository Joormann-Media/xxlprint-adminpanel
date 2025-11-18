<?php

namespace App\Controller;

use App\Entity\GeoCoordinate;
use App\Form\GeoCoordinateType;
use App\Repository\GeoCoordinateRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/geo/coordinate')]
final class GeoCoordinateController extends AbstractController
{
    #[Route(name: 'app_geo_coordinate_index', methods: ['GET'])]
    public function index(GeoCoordinateRepository $geoCoordinateRepository): Response
    {
        return $this->render('geo_coordinate/index.html.twig', [
            'geo_coordinates' => $geoCoordinateRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_geo_coordinate_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $geoCoordinate = new GeoCoordinate();
        $form = $this->createForm(GeoCoordinateType::class, $geoCoordinate);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($geoCoordinate);
            $entityManager->flush();

            return $this->redirectToRoute('app_geo_coordinate_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('geo_coordinate/new.html.twig', [
            'geo_coordinate' => $geoCoordinate,
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_geo_coordinate_show', methods: ['GET'])]
    public function show(GeoCoordinate $geoCoordinate): Response
    {
        return $this->render('geo_coordinate/show.html.twig', [
            'geo_coordinate' => $geoCoordinate,
        ]);
    }

    #[Route('/{id<\d+>}/edit', name: 'app_geo_coordinate_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, GeoCoordinate $geoCoordinate, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(GeoCoordinateType::class, $geoCoordinate);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_geo_coordinate_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('geo_coordinate/edit.html.twig', [
            'geo_coordinate' => $geoCoordinate,
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_geo_coordinate_delete', methods: ['POST'])]
    public function delete(Request $request, GeoCoordinate $geoCoordinate, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$geoCoordinate->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($geoCoordinate);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_geo_coordinate_index', [], Response::HTTP_SEE_OTHER);
    }
}
