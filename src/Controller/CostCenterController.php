<?php

namespace App\Controller;

use App\Entity\CostCenter;
use App\Form\CostCenterType;
use App\Repository\CostCenterRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/cost/center')]
final class CostCenterController extends AbstractController
{
    #[Route(name: 'app_cost_center_index', methods: ['GET'])]
    public function index(CostCenterRepository $costCenterRepository): Response
    {
        return $this->render('cost_center/index.html.twig', [
            'cost_centers' => $costCenterRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_cost_center_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $costCenter = new CostCenter();
        $form = $this->createForm(CostCenterType::class, $costCenter);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($costCenter);
            $entityManager->flush();

            return $this->redirectToRoute('app_cost_center_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('cost_center/new.html.twig', [
            'cost_center' => $costCenter,
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_cost_center_show', methods: ['GET'])]
    public function show(CostCenter $costCenter): Response
    {
        return $this->render('cost_center/show.html.twig', [
            'cost_center' => $costCenter,
        ]);
    }

    #[Route('/{id<\d+>}/edit', name: 'app_cost_center_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, CostCenter $costCenter, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CostCenterType::class, $costCenter);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_cost_center_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('cost_center/edit.html.twig', [
            'cost_center' => $costCenter,
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_cost_center_delete', methods: ['POST'])]
    public function delete(Request $request, CostCenter $costCenter, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$costCenter->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($costCenter);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_cost_center_index', [], Response::HTTP_SEE_OTHER);
    }
}
