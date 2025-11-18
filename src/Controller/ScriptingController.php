<?php

namespace App\Controller;

use App\Entity\Scripting;
use App\Form\ScriptingForm;
use App\Repository\ScriptingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/scripting')]
final class ScriptingController extends AbstractController
{
    #[Route(name: 'app_scripting_index', methods: ['GET'])]
    public function index(ScriptingRepository $scriptingRepository): Response
    {
        return $this->render('scripting/index.html.twig', [
            'scriptings' => $scriptingRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_scripting_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $scripting = new Scripting();
        $form = $this->createForm(ScriptingForm::class, $scripting);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($scripting);
            $entityManager->flush();

            return $this->redirectToRoute('app_scripting_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('scripting/new.html.twig', [
            'scripting' => $scripting,
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_scripting_show', methods: ['GET'])]
    public function show(Scripting $scripting): Response
    {
        return $this->render('scripting/show.html.twig', [
            'scripting' => $scripting,
        ]);
    }

    #[Route('/{id<\d+>}/edit', name: 'app_scripting_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Scripting $scripting, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ScriptingForm::class, $scripting);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_scripting_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('scripting/edit.html.twig', [
            'scripting' => $scripting,
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_scripting_delete', methods: ['POST'])]
    public function delete(Request $request, Scripting $scripting, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$scripting->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($scripting);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_scripting_index', [], Response::HTTP_SEE_OTHER);
    }
}
