<?php

namespace App\Controller;

use App\Entity\SshApiKeys;
use App\Form\SshApiKeysForm;
use App\Repository\SshApiKeysRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/ssh/api/keys')]
final class SshApiKeysController extends AbstractController
{
    #[Route(name: 'app_ssh_api_keys_index', methods: ['GET'])]
    public function index(SshApiKeysRepository $sshApiKeysRepository): Response
    {
        return $this->render('ssh_api_keys/index.html.twig', [
            'ssh_api_keys' => $sshApiKeysRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_ssh_api_keys_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $sshApiKey = new SshApiKeys();
        $form = $this->createForm(SshApiKeysForm::class, $sshApiKey);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($sshApiKey);
            $entityManager->flush();

            return $this->redirectToRoute('app_ssh_api_keys_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('ssh_api_keys/new.html.twig', [
            'ssh_api_key' => $sshApiKey,
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_ssh_api_keys_show', methods: ['GET'])]
    public function show(SshApiKeys $sshApiKey): Response
    {
        return $this->render('ssh_api_keys/show.html.twig', [
            'ssh_api_key' => $sshApiKey,
        ]);
    }

    #[Route('/{id<\d+>}/edit', name: 'app_ssh_api_keys_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, SshApiKeys $sshApiKey, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(SshApiKeysForm::class, $sshApiKey);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_ssh_api_keys_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('ssh_api_keys/edit.html.twig', [
            'ssh_api_key' => $sshApiKey,
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_ssh_api_keys_delete', methods: ['POST'])]
    public function delete(Request $request, SshApiKeys $sshApiKey, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$sshApiKey->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($sshApiKey);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_ssh_api_keys_index', [], Response::HTTP_SEE_OTHER);
    }
}
