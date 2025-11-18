<?php

namespace App\Controller;

use App\Entity\DevLogEntry;
use App\Form\DevLogEntryType;
use App\Repository\DevLogEntryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use App\Repository\UserRepository;
use Symfony\Bundle\SecurityBundle\Security; // Wichtig für aktuellen User

#[Route('/dev-log/entry')]
final class DevLogEntryController extends AbstractController
{
    #[Route(name: 'app_dev_log_entry_index', methods: ['GET'])]
    public function index(DevLogEntryRepository $devLogEntryRepository): Response
    {
        return $this->render('dev_log_entry/index.html.twig', [
            'dev_log_entries' => $devLogEntryRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_dev_log_entry_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, Security $security): Response
    {
        $devLogEntry = new DevLogEntry();
        $form = $this->createForm(DevLogEntryType::class, $devLogEntry);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $devLogEntry->setCreatedAt(new \DateTime());
            $devLogEntry->setUpdatedAt(new \DateTime());

            // User setzen (der aktuell eingeloggte User)
            $user = $security->getUser();
            if ($user) {
                $devLogEntry->setUser($user);
            }

            $entityManager->persist($devLogEntry);
            $entityManager->flush();

            return $this->redirectToRoute('app_dev_log_entry_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('dev_log_entry/new.html.twig', [
            'dev_log_entry' => $devLogEntry,
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_dev_log_entry_show', methods: ['GET'])]
    public function show(DevLogEntry $devLogEntry): Response
    {
        return $this->render('dev_log_entry/show.html.twig', [
            'dev_log_entry' => $devLogEntry,
        ]);
    }

    #[Route('/{id<\d+>}/edit', name: 'app_dev_log_entry_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, DevLogEntry $devLogEntry, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(DevLogEntryType::class, $devLogEntry);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Nur updatedAt aktualisieren (createdAt und User bleiben gleich)
            $devLogEntry->setUpdatedAt(new \DateTime());

            $entityManager->flush();

            return $this->redirectToRoute('app_dev_log_entry_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('dev_log_entry/edit.html.twig', [
            'dev_log_entry' => $devLogEntry,
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_dev_log_entry_delete', methods: ['POST'])]
    public function delete(Request $request, DevLogEntry $devLogEntry, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$devLogEntry->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($devLogEntry);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_dev_log_entry_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/import', name: 'app_dev_log_entry_import', methods: ['POST'])]
    public function import(
        Request $request,
        EntityManagerInterface $entityManager,
        UserRepository $userRepository,
        SerializerInterface $serializer
    ): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // Minimal-Validierung
        if (!$data || empty($data['title']) || empty($data['content']) || empty($data['user'])) {
            return new JsonResponse(['status' => 'error', 'message' => 'Felder fehlen (title, content, user)!'], 400);
        }

        // User holen
        $user = $userRepository->find($data['user']);
        if (!$user) {
            return new JsonResponse(['status' => 'error', 'message' => 'User nicht gefunden!'], 404);
        }

        $devLogEntry = new DevLogEntry();
        $devLogEntry->setTitle($data['title']);
        $devLogEntry->setContent($data['content']);
        $devLogEntry->setUser($user);

        // createdAt & updatedAt (optional, sonst Default = jetzt)
        if (!empty($data['createdAt'])) {
            $devLogEntry->setCreatedAt(new \DateTime($data['createdAt']));
        } else {
            $devLogEntry->setCreatedAt(new \DateTime());
        }
        if (!empty($data['updatedAt'])) {
            $devLogEntry->setUpdatedAt(new \DateTime($data['updatedAt']));
        } else {
            $devLogEntry->setUpdatedAt(new \DateTime());
        }

        $entityManager->persist($devLogEntry);
        $entityManager->flush();

        return new JsonResponse([
            'status' => 'ok',
            'id' => $devLogEntry->getId(),
            'createdAt' => $devLogEntry->getCreatedAt()?->format('Y-m-d H:i:s'),
        ]);
    }
}
