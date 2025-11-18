<?php

namespace App\Controller;

use App\Entity\CityPostalcode;
use App\Form\CityPostalcodeType;
use App\Repository\CityPostalcodeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/city/postalcode')]
final class CityPostalcodeController extends AbstractController
{
    #[Route(name: 'app_city_postalcode_index', methods: ['GET'])]
    public function index(CityPostalcodeRepository $cityPostalcodeRepository): Response
    {
        return $this->render('city_postalcode/index.html.twig', [
            'city_postalcodes' => $cityPostalcodeRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_city_postalcode_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $cityPostalcode = new CityPostalcode();
        $form = $this->createForm(CityPostalcodeType::class, $cityPostalcode);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($cityPostalcode);
            $entityManager->flush();

            return $this->redirectToRoute('app_city_postalcode_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('city_postalcode/new.html.twig', [
            'city_postalcode' => $cityPostalcode,
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_city_postalcode_show', methods: ['GET'])]
    public function show(CityPostalcode $cityPostalcode): Response
    {
        return $this->render('city_postalcode/show.html.twig', [
            'city_postalcode' => $cityPostalcode,
        ]);
    }

    #[Route('/{id<\d+>}/edit', name: 'app_city_postalcode_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, CityPostalcode $cityPostalcode, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CityPostalcodeType::class, $cityPostalcode);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_city_postalcode_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('city_postalcode/edit.html.twig', [
            'city_postalcode' => $cityPostalcode,
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_city_postalcode_delete', methods: ['POST'])]
    public function delete(Request $request, CityPostalcode $cityPostalcode, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$cityPostalcode->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($cityPostalcode);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_city_postalcode_index', [], Response::HTTP_SEE_OTHER);
    }
}
