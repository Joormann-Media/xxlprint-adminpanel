<?php

namespace App\Controller;

use App\Entity\School;
use App\Entity\SchoolTour;
use App\Form\SchoolTourType;
use App\Repository\SchoolTourRepository;
use App\Repository\SchoolkidsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/school-tour')]
final class SchoolTourController extends AbstractController
{
    #[Route(name: 'app_school_tour_index', methods: ['GET'])]
    public function index(SchoolTourRepository $schoolTourRepository): Response
    {
        return $this->render('school_tour/index.html.twig', [
            'school_tours' => $schoolTourRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_school_tour_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $schoolTour = new SchoolTour(); // ✅ setzt Defaults (siehe Entity __construct)
        $form = $this->createForm(SchoolTourType::class, $schoolTour);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if (!$form->isValid()) {
                foreach ($form->getErrors(true) as $error) {
                    $this->addFlash('danger', sprintf(
                        'Fehler im Feld "%s": %s',
                        $error->getOrigin()?->getName() ?? 'unbekannt',
                        $error->getMessage()
                    ));
                }
            }

            if ($form->isValid()) {
                $entityManager->persist($schoolTour);
                $entityManager->flush();

                $this->addFlash('success', 'Neue Tour erfolgreich angelegt.');
                return $this->redirectToRoute('app_school_tour_index');
            }
        }

        return $this->render('school_tour/new.html.twig', [
            'school_tour' => $schoolTour,
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}/edit', name: 'app_school_tour_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, SchoolTour $schoolTour, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(SchoolTourType::class, $schoolTour);
        $form->handleRequest($request);

        if ($form->isSubmitted() && !$form->isValid()) {
    foreach ($form->getErrors(true) as $error) {
        dump([
            'field' => $error->getOrigin()?->getName(),
            'message' => $error->getMessage(),
        ]);
    }
}


        if ($form->isSubmitted()) {
            if (!$form->isValid()) {
                foreach ($form->getErrors(true) as $error) {
                    $this->addFlash('danger', sprintf(
                        'Fehler im Feld "%s": %s',
                        $error->getOrigin()?->getName() ?? 'unbekannt',
                        $error->getMessage()
                    ));
                }
            }

            if ($form->isValid()) {
                $entityManager->flush();

                $this->addFlash('success', 'Tour erfolgreich aktualisiert.');
                return $this->redirectToRoute('app_school_tour_index');
            }
        }

        return $this->render('school_tour/edit.html.twig', [
            'school_tour' => $schoolTour,
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_school_tour_show', methods: ['GET'])]
    public function show(SchoolTour $schoolTour): Response
    {
        return $this->render('school_tour/show.html.twig', [
            'school_tour' => $schoolTour,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_school_tour_delete', methods: ['POST'])]
    public function delete(Request $request, SchoolTour $schoolTour, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$schoolTour->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($schoolTour);
            $entityManager->flush();

            $this->addFlash('success', 'Tour gelöscht.');
        }

        return $this->redirectToRoute('app_school_tour_index');
    }

    // 🚀 NEUER API-ENDPUNKT
    #[Route('/{id<\d+>}/kids', name: 'app_school_tour_kids', methods: ['GET'])]
    public function kids(SchoolTour $schoolTour, SchoolkidsRepository $repo): JsonResponse
    {
        // Nur Kids der Schule abrufen
        $kids = $repo->findBy(['school' => $schoolTour->getSchool()]);

        $data = array_map(fn($k) => [
            'id' => $k->getId(),
            'name' => $k->getFirstName() . ' ' . $k->getLastName(),
            'lat' => $k->getLatitude(),
            'lng' => $k->getLongitude(),
            'address' => $k->getStreet() . ' ' . $k->getStreetNumber() . ', ' . $k->getZip() . ' ' . $k->getCity(),
            'requiredSeats' => $k->getRequiredSeats(),
            'needsAid' => $k->isNeedsAid(),
            'aidType' => $k->getAidType(),
            'hasCompanion' => $k->hasCompanion(),
            'companionName' => $k->getCompanionName(),
        ], $kids);

        return $this->json($data);
    }
    #[Route('/school/{id<\d+>}/kids', name: 'app_school_kids', methods: ['GET'])]
    public function kidsBySchool(School $school, SchoolkidsRepository $repo): JsonResponse
    {
        $kids = $repo->findBy(['school' => $school]);

        $data = array_map(fn($k) => [
            'id' => $k->getId(),
            'name' => $k->getFirstName().' '.$k->getLastName(),
            'lat' => $k->getLatitude(),
            'lng' => $k->getLongitude(),
            'address' => $k->getStreet().' '.$k->getStreetNumber().', '.$k->getZip().' '.$k->getCity(),
            'requiredSeats' => $k->getRequiredSeats(),
            'needsAid' => $k->isNeedsAid(),
            'aidType' => $k->getAidType(),
            'hasCompanion' => $k->hasCompanion(),
            'companionName' => $k->getCompanionName(),
        ], $kids);

        return $this->json($data);
    }
#[Route('/map', name: 'app_school_tour_map_generic', methods: ['GET'])]
public function mapGeneric(): Response
{
    return $this->render('school_tour/map.html.twig');
}

#[Route('/map/{id<\d+>}', name: 'app_school_tour_map', methods: ['GET'])]
public function map(SchoolTour $schoolTour): Response
{
    return $this->render('school_tour/map.html.twig', [
        'school_tour' => $schoolTour,
        'saved_route' => $schoolTour->getRoute()
    ]);
}


#[Route('/{id<\d+>}/available-kids', name: 'app_school_tour_available_kids', methods: ['GET'])]
public function availableKids(SchoolTour $schoolTour, SchoolkidsRepository $repo): JsonResponse
{
    $allKids = $repo->findBy(['school' => $schoolTour->getSchool()]);
    $assignedKids = [];

    foreach ($schoolTour->getStops() as $stop) {
        foreach ($stop->getKids() as $kid) {
            $assignedKids[$kid->getId()] = true;
        }
    }

    $data = array_values(array_map(fn($k) => [
        'id' => $k->getId(),
        'name' => $k->getFirstName() . ' ' . $k->getLastName(),
        'address' => $k->getStreet().' '.$k->getStreetNumber().', '.$k->getZip().' '.$k->getCity(),
        'lat' => $k->getLatitude(),
        'lng' => $k->getLongitude(),
        'requiredSeats' => $k->getRequiredSeats(),
        'needsAid' => $k->isNeedsAid(),
        'aidType' => $k->getAidType(),
        'hasCompanion' => $k->hasCompanion(),
        'companionName' => $k->getCompanionName(),
    ], array_filter($allKids, fn($k) => !isset($assignedKids[$k->getId()]))));

    return $this->json($data);
}
#[Route('/stop/{stopId}/assign/{kidId}', name: 'app_stop_assign_kid', methods: ['POST'])]
public function assignKid(int $stopId, int $kidId, EntityManagerInterface $em): JsonResponse
{
    $stop = $em->getRepository(SchoolTourStop::class)->find($stopId);
    $kid = $em->getRepository(Schoolkids::class)->find($kidId);

    if (!$stop || !$kid) {
        return $this->json(['success' => false], 404);
    }

    $stop->addKid($kid);
    $em->flush();

    return $this->json(['success' => true]);
}


}
