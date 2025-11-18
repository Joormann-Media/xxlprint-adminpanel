<?php

namespace App\Controller;

use App\Entity\State;
use App\Form\StateType;
use App\Repository\StateRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;


#[Route('/state')]
final class StateController extends AbstractController
{
    #[Route(name: 'app_state_index', methods: ['GET'])]
    public function index(StateRepository $stateRepository): Response
    {
        return $this->render('state/index.html.twig', [
            'states' => $stateRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_state_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $state = new State();
        $form = $this->createForm(StateType::class, $state);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($state);
            $entityManager->flush();

            return $this->redirectToRoute('app_state_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('state/new.html.twig', [
            'state' => $state,
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_state_show', methods: ['GET'])]
    public function show(State $state): Response
    {
        return $this->render('state/show.html.twig', [
            'state' => $state,
        ]);
    }

    #[Route('/{id<\d+>}/edit', name: 'app_state_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, State $state, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(StateType::class, $state);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_state_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('state/edit.html.twig', [
            'state' => $state,
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_state_delete', methods: ['POST'])]
    public function delete(Request $request, State $state, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$state->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($state);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_state_index', [], Response::HTTP_SEE_OTHER);
    }

        #[Route('/import', name: 'app_state_import', methods: ['GET', 'POST'])]
    public function import(EntityManagerInterface $em, StateRepository $stateRepo): Response
    {
        // Liste aller Bundesländer als Array [code, name, latitude, longitude]
        $states = [
            // Codes laut Wikipedia & OSM, Koordinaten grob Mittelpunkt des Bundeslandes
            ['code' => 'BW', 'name' => 'Baden-Württemberg', 'lat' => 48.6616, 'lon' => 9.3501],
            ['code' => 'BY', 'name' => 'Bayern', 'lat' => 48.7904, 'lon' => 11.4979],
            ['code' => 'BE', 'name' => 'Berlin', 'lat' => 52.5200, 'lon' => 13.4050],
            ['code' => 'BB', 'name' => 'Brandenburg', 'lat' => 52.4125, 'lon' => 12.5316],
            ['code' => 'HB', 'name' => 'Bremen', 'lat' => 53.0793, 'lon' => 8.8017],
            ['code' => 'HH', 'name' => 'Hamburg', 'lat' => 53.5511, 'lon' => 9.9937],
            ['code' => 'HE', 'name' => 'Hessen', 'lat' => 50.6521, 'lon' => 9.1624],
            ['code' => 'MV', 'name' => 'Mecklenburg-Vorpommern', 'lat' => 53.6127, 'lon' => 12.4296],
            ['code' => 'NI', 'name' => 'Niedersachsen', 'lat' => 52.6367, 'lon' => 9.8451],
            ['code' => 'NW', 'name' => 'Nordrhein-Westfalen', 'lat' => 51.4332, 'lon' => 7.6616],
            ['code' => 'RP', 'name' => 'Rheinland-Pfalz', 'lat' => 50.1183, 'lon' => 7.3087],
            ['code' => 'SL', 'name' => 'Saarland', 'lat' => 49.3964, 'lon' => 7.0220],
            ['code' => 'SN', 'name' => 'Sachsen', 'lat' => 51.1045, 'lon' => 13.2017],
            ['code' => 'ST', 'name' => 'Sachsen-Anhalt', 'lat' => 51.9503, 'lon' => 11.6923],
            ['code' => 'SH', 'name' => 'Schleswig-Holstein', 'lat' => 54.2194, 'lon' => 9.6961],
            ['code' => 'TH', 'name' => 'Thüringen', 'lat' => 50.9011, 'lon' => 11.0378],
        ];

        $inserted = 0;
        $skipped = 0;
        foreach ($states as $s) {
            // Existiert bereits?
            $existing = $stateRepo->findOneBy(['code' => $s['code']]);
            if ($existing) {
                $skipped++;
                continue;
            }
            $state = new State();
            $state->setCode($s['code'])
                ->setName($s['name'])
                ->setLatitude($s['lat'])
                ->setLongitude($s['lon']);
            $em->persist($state);
            $inserted++;
        }
        $em->flush();

        $this->addFlash('success', "$inserted Bundesländer importiert. $skipped übersprungen (existierten schon).");
        return $this->redirectToRoute('app_state_index');
    }
#[Route('/api/import', name: 'app_state_api_import', methods: ['POST'])]
public function apiImport(Request $request, EntityManagerInterface $em, StateRepository $stateRepo): JsonResponse
{
    $data = json_decode($request->getContent(), true);

    if (!$data || empty($data['code']) || empty($data['name'])) {
        return new JsonResponse(['error' => 'Missing code or name'], 400);
    }

    // Prüfe auf existierenden State via Code
    $existing = $stateRepo->findOneBy(['code' => $data['code']]);
    if ($existing) {
        // Optional: Update Polygon, wenn leer/neu?
        if (!empty($data['polygon']) && empty($existing->getPolygon())) {
            $existing->setPolygon($data['polygon']);
            $em->flush();
            return new JsonResponse(['status' => 'updated', 'id' => $existing->getId()]);
        }
        return new JsonResponse(['status' => 'exists', 'id' => $existing->getId()]);
    }

    // Neu anlegen
    $state = new State();
    $state->setCode($data['code'])
        ->setName($data['name'])
        ->setLatitude($data['lat'] ?? null)
        ->setLongitude($data['lon'] ?? null)
        ->setPolygon($data['polygon'] ?? null);

    $em->persist($state);
    $em->flush();

    return new JsonResponse(['status' => 'created', 'id' => $state->getId()]);
}

}
