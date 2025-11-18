<?php

namespace App\Controller;

use App\Entity\ProjectStatistics;
use App\Form\ProjectStatisticsType;
use App\Repository\ProjectStatisticsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/project-statistics')]
final class ProjectStatisticsController extends AbstractController
{
    #[Route(name: 'app_project_statistics_index', methods: ['GET'])]
    public function index(ProjectStatisticsRepository $projectStatisticsRepository): Response
    {
        return $this->render('project_statistics/index.html.twig', [
            'project_statistics' => $projectStatisticsRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_project_statistics_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $projectStatistic = new ProjectStatistics();
        $form = $this->createForm(ProjectStatisticsType::class, $projectStatistic);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($projectStatistic);
            $entityManager->flush();

            return $this->redirectToRoute('app_project_statistics_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('project_statistics/new.html.twig', [
            'project_statistic' => $projectStatistic,
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_project_statistics_show', methods: ['GET'])]
    public function show(ProjectStatistics $projectStatistic): Response
    {
        return $this->render('project_statistics/show.html.twig', [
            'project_statistic' => $projectStatistic,
        ]);
    }

    #[Route('/{id<\d+>}/edit', name: 'app_project_statistics_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ProjectStatistics $projectStatistic, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ProjectStatisticsType::class, $projectStatistic);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_project_statistics_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('project_statistics/edit.html.twig', [
            'project_statistic' => $projectStatistic,
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_project_statistics_delete', methods: ['POST'])]
    public function delete(Request $request, ProjectStatistics $projectStatistic, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$projectStatistic->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($projectStatistic);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_project_statistics_index', [], Response::HTTP_SEE_OTHER);
    }
    #[Route('/import', name: 'api_project_statistics_create', methods: ['POST'])]
// Optional: #[IsGranted('ROLE_DEV')] für Auth-Schutz!
public function apiCreate(
    Request $request,
    EntityManagerInterface $em,
    SerializerInterface $serializer
): JsonResponse {
    // JSON einlesen
    $data = json_decode($request->getContent(), true);

    if (!$data) {
        return new JsonResponse(['error' => 'Invalid JSON'], 400);
    }

    // Hydrieren via Serializer (macht das Leben leichter!)
    $projectStat = $serializer->denormalize($data, ProjectStatistics::class);

    $em->persist($projectStat);
    $em->flush();

    return new JsonResponse(['status' => 'ok', 'id' => $projectStat->getId()]);
}
}
