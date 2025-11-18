<?php

namespace App\Controller;

use App\Entity\StopPoint;
use App\Form\StopPointType;
use App\Repository\StopPointRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/stop-point')]
final class StopPointController extends AbstractController
{
    #[Route(name: 'app_stop_point_index', methods: ['GET'])]
    public function index(StopPointRepository $stopPointRepository): Response
    {
        return $this->render('stop_point/index.html.twig', [
            'stop_points' => $stopPointRepository->findAll(),
            'page_title' => 'Haltestellen - Übersicht',
        ]);
    }

    #[Route('/new', name: 'app_stop_point_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $stopPoint = new StopPoint();
        $form = $this->createForm(StopPointType::class, $stopPoint);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // Karten-Ausschnitt übernehmen (aus hidden field)
            $mapViewport = $form->get('mapViewport')->getData();
            if ($mapViewport) {
                $stopPoint->setMapViewport($mapViewport);
            }

            $entityManager->persist($stopPoint);
            $entityManager->flush();

            $this->addFlash('success', 'Haltepunkt wurde gespeichert.');
            return $this->redirectToRoute('app_stop_point_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('stop_point/new.html.twig', [
            'stop_point' => $stopPoint,
            'form' => $form,
            'page_title' => 'Neue Haltestelle anlegen',
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_stop_point_show', methods: ['GET'])]
    public function show(StopPoint $stopPoint): Response
    {
        $viewportJson = $stopPoint->getMapViewport();
        return $this->render('stop_point/show.html.twig', [
            'stop_point' => $stopPoint,
            'page_title' => 'Haltestellen Details',
        ]);
    }

    #[Route('/{id<\d+>}/edit', name: 'app_stop_point_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, StopPoint $stopPoint, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(StopPointType::class, $stopPoint);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $mapViewport = $form->get('mapViewport')->getData();
            if ($mapViewport) {
                $stopPoint->setMapViewport($mapViewport);
            }

            $entityManager->flush();

            $this->addFlash('success', 'Haltepunkt wurde aktualisiert.');
            return $this->redirectToRoute('app_stop_point_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('stop_point/edit.html.twig', [
            'stop_point' => $stopPoint,
            'form' => $form,
            'page_title' => 'Haltestelle bearbeiten',
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_stop_point_delete', methods: ['POST'])]
    public function delete(Request $request, StopPoint $stopPoint, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$stopPoint->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($stopPoint);
            $entityManager->flush();
            $this->addFlash('info', 'Haltepunkt wurde gelöscht.');
        }

        return $this->redirectToRoute('app_stop_point_index', [], Response::HTTP_SEE_OTHER);
    }
}
