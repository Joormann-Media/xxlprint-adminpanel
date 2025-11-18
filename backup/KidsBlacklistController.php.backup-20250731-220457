<?php

namespace App\Controller;

use App\Entity\KidsBlacklist;
use App\Form\KidsBlacklistType;
use App\Repository\KidsBlacklistRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/kids/blacklist')]
final class KidsBlacklistController extends AbstractController
{
    #[Route(name: 'app_kids_blacklist_index', methods: ['GET'])]
    public function index(KidsBlacklistRepository $kidsBlacklistRepository): Response
    {
        return $this->render('kids_blacklist/index.html.twig', [
            'kids_blacklists' => $kidsBlacklistRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_kids_blacklist_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $kidsBlacklist = new KidsBlacklist();
        $form = $this->createForm(KidsBlacklistType::class, $kidsBlacklist);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($kidsBlacklist);
            $entityManager->flush();

            return $this->redirectToRoute('app_kids_blacklist_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('kids_blacklist/new.html.twig', [
            'kids_blacklist' => $kidsBlacklist,
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_kids_blacklist_show', methods: ['GET'])]
    public function show(KidsBlacklist $kidsBlacklist): Response
    {
        return $this->render('kids_blacklist/show.html.twig', [
            'kids_blacklist' => $kidsBlacklist,
        ]);
    }

    #[Route('/{id<\d+>}/edit', name: 'app_kids_blacklist_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, KidsBlacklist $kidsBlacklist, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(KidsBlacklistType::class, $kidsBlacklist);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_kids_blacklist_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('kids_blacklist/edit.html.twig', [
            'kids_blacklist' => $kidsBlacklist,
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_kids_blacklist_delete', methods: ['POST'])]
    public function delete(Request $request, KidsBlacklist $kidsBlacklist, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$kidsBlacklist->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($kidsBlacklist);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_kids_blacklist_index', [], Response::HTTP_SEE_OTHER);
    }
}
