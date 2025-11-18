<?php

namespace App\Controller;

use App\Entity\OfficialAddresses;
use App\Form\OfficialAddressesType;
use App\Repository\OfficialAddressesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/official/addresses')]
final class OfficialAddressesController extends AbstractController
{
    #[Route(name: 'app_official_addresses_index', methods: ['GET'])]
    public function index(OfficialAddressesRepository $officialAddressesRepository): Response
    {
        return $this->render('official_addresses/index.html.twig', [
            'official_addresses' => $officialAddressesRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_official_addresses_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $officialAddress = new OfficialAddresses();
        $form = $this->createForm(OfficialAddressesType::class, $officialAddress);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($officialAddress);
            $entityManager->flush();

            return $this->redirectToRoute('app_official_addresses_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('official_addresses/new.html.twig', [
            'official_address' => $officialAddress,
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_official_addresses_show', methods: ['GET'])]
    public function show(OfficialAddresses $officialAddress): Response
    {
        return $this->render('official_addresses/show.html.twig', [
            'official_address' => $officialAddress,
        ]);
    }

    #[Route('/{id<\d+>}/edit', name: 'app_official_addresses_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, OfficialAddresses $officialAddress, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(OfficialAddressesType::class, $officialAddress);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_official_addresses_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('official_addresses/edit.html.twig', [
            'official_address' => $officialAddress,
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_official_addresses_delete', methods: ['POST'])]
    public function delete(Request $request, OfficialAddresses $officialAddress, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$officialAddress->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($officialAddress);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_official_addresses_index', [], Response::HTTP_SEE_OTHER);
    }
}
