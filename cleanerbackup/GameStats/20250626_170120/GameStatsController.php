<?php

namespace App\Controller;

use App\Entity\GameStats;
use App\Form\GameStatsForm;
use App\Repository\GameStatsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/game/stats')]
final class GameStatsController extends AbstractController
{
    #[Route(name: 'app_game_stats_index', methods: ['GET'])]
    public function index(GameStatsRepository $gameStatsRepository): Response
    {
        return $this->render('game_stats/index.html.twig', [
            'game_stats' => $gameStatsRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_game_stats_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $gameStat = new GameStats();
        $form = $this->createForm(GameStatsForm::class, $gameStat);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($gameStat);
            $entityManager->flush();

            return $this->redirectToRoute('app_game_stats_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('game_stats/new.html.twig', [
            'game_stat' => $gameStat,
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_game_stats_show', methods: ['GET'])]
    public function show(GameStats $gameStat): Response
    {
        return $this->render('game_stats/show.html.twig', [
            'game_stat' => $gameStat,
        ]);
    }

    #[Route('/{id<\d+>}/edit', name: 'app_game_stats_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, GameStats $gameStat, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(GameStatsForm::class, $gameStat);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_game_stats_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('game_stats/edit.html.twig', [
            'game_stat' => $gameStat,
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_game_stats_delete', methods: ['POST'])]
    public function delete(Request $request, GameStats $gameStat, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$gameStat->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($gameStat);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_game_stats_index', [], Response::HTTP_SEE_OTHER);
    }
}
