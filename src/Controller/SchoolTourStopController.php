<?php

namespace App\Controller;

use App\Entity\SchoolTourStop;
use App\Form\SchoolTourStopType;
use App\Repository\SchoolTourStopRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\SchoolTour;
use Symfony\Component\HttpFoundation\JsonResponse;

#[Route('/school-tour/stop')]
final class SchoolTourStopController extends AbstractController
{
    #[Route(name: 'app_school_tour_stop_index', methods: ['GET'])]
    public function index(SchoolTourStopRepository $schoolTourStopRepository): Response
    {
        return $this->render('school_tour_stop/index.html.twig', [
            'school_tour_stops' => $schoolTourStopRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_school_tour_stop_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $schoolTourStop = new SchoolTourStop();
        $form = $this->createForm(SchoolTourStopType::class, $schoolTourStop);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($schoolTourStop);
            $entityManager->flush();

            return $this->redirectToRoute('app_school_tour_stop_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('school_tour_stop/new.html.twig', [
            'school_tour_stop' => $schoolTourStop,
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_school_tour_stop_show', methods: ['GET'])]
    public function show(SchoolTourStop $schoolTourStop): Response
    {
        return $this->render('school_tour_stop/show.html.twig', [
            'school_tour_stop' => $schoolTourStop,
        ]);
    }

    #[Route('/{id<\d+>}/edit', name: 'app_school_tour_stop_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, SchoolTourStop $schoolTourStop, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(SchoolTourStopType::class, $schoolTourStop);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_school_tour_stop_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('school_tour_stop/edit.html.twig', [
            'school_tour_stop' => $schoolTourStop,
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_school_tour_stop_delete', methods: ['POST'])]
    public function delete(Request $request, SchoolTourStop $schoolTourStop, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$schoolTourStop->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($schoolTourStop);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_school_tour_stop_index', [], Response::HTTP_SEE_OTHER);
    }
    #[Route('/tour/{id<\d+>}/add', name: 'app_school_tour_stop_add', methods: ['POST'])]
    public function addStop(Request $request, SchoolTour $tour, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $stop = new SchoolTourStop();
        $stop->setTour($tour);
        $stop->setAddress($data['address'] ?? null);
        $stop->setLatitude($data['lat'] ?? null);
        $stop->setLongitude($data['lng'] ?? null);
        $stop->setPlannedTime(isset($data['plannedTime']) ? new \DateTime($data['plannedTime']) : null);
        $stop->setSortOrder($data['sortOrder'] ?? 0);
        $stop->setNotes($data['notes'] ?? null);

        $em->persist($stop);
        $em->flush();

        return $this->json(['success' => true, 'id' => $stop->getId()]);
    }

    // 🚀 API: Alle Stops einer Tour abrufen
#[Route('/tour/{id<\d+>}', name: 'app_school_tour_stops_by_tour', methods: ['GET'])]
public function stopsByTour(SchoolTour $tour, SchoolTourStopRepository $repo): JsonResponse
{
    $stops = $repo->findBy(['tour' => $tour], ['sortOrder' => 'ASC']);

    $data = array_map(fn(SchoolTourStop $s) => [
        'id'          => $s->getId(),
        'sortOrder'   => $s->getSortOrder(),
        'plannedTime' => $s->getPlannedTime()?->format('H:i'),
        'notes'       => $s->getNotes(),
        'lat'         => $s->getLatitude(),
        'lng'         => $s->getLongitude(),
        'address'     => $s->getAddress(),
        'kid'         => $s->getKid() ? [
            'id'   => $s->getKid()->getId(),
            'name' => $s->getKid()->getFirstName().' '.$s->getKid()->getLastName(),
        ] : null,
        'school'      => $s->getSchool() ? [
            'id'      => $s->getSchool()->getId(),
            'name'    => $s->getSchool()->getName(),
            'address' => trim($s->getSchool()->getStreet().' '.$s->getSchool()->getStreetNo().', '.$s->getSchool()->getZip().' '.$s->getSchool()->getCity()),
            'lat'     => $s->getSchool()->getLatitude(),
            'lng'     => $s->getSchool()->getLongitude(),
        ] : null,
    ], $stops);

    return $this->json([
        'tour'  => [
            'id'   => $tour->getId(),
            'name' => $tour->getName(),
            'type' => $tour->getType(),
        ],
        'stops' => $data,
    ]);
}
#[Route('/{id<\d+>}/update', name: 'app_school_tour_stop_update', methods: ['PATCH'])]
public function updateStop(Request $request, SchoolTourStop $stop, EntityManagerInterface $em): JsonResponse
{
    $data = json_decode($request->getContent(), true);

    if (isset($data['address'])) $stop->setAddress($data['address']);
    if (isset($data['lat'])) $stop->setLatitude($data['lat']);
    if (isset($data['lng'])) $stop->setLongitude($data['lng']);
    if (isset($data['plannedTime'])) $stop->setPlannedTime(new \DateTime($data['plannedTime']));
    if (isset($data['sortOrder'])) $stop->setSortOrder($data['sortOrder']);
    if (isset($data['notes'])) $stop->setNotes($data['notes']);

    $em->flush();

    return $this->json(['success' => true]);
}

#[Route('/{id<\d+>}/delete', name: 'app_school_tour_stop_api_delete', methods: ['DELETE'])]
public function apiDelete(SchoolTourStop $stop, EntityManagerInterface $em): JsonResponse
{
    $em->remove($stop);
    $em->flush();

    return $this->json(['success' => true]);
}


}
