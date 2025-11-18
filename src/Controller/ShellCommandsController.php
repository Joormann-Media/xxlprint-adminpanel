<?php

namespace App\Controller;

use App\Entity\ShellCommands;
use App\Form\ShellCommandsForm;
use App\Repository\ShellCommandsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Entity\User;

#[Route('/shell-commands')]
final class ShellCommandsController extends AbstractController
{
    #[Route(name: 'app_shell_commands_index', methods: ['GET'])]
    public function index(ShellCommandsRepository $shellCommandsRepository): Response
    {
        return $this->render('shell_commands/index.html.twig', [
            'shell_commands' => $shellCommandsRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_shell_commands_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $shellCommand = new ShellCommands();
        $form = $this->createForm(ShellCommandsForm::class, $shellCommand);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($shellCommand);
            $entityManager->flush();

            return $this->redirectToRoute('app_shell_commands_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('shell_commands/new.html.twig', [
            'shell_command' => $shellCommand,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_shell_commands_show', methods: ['GET'])]
    public function show(ShellCommands $shellCommand): Response
    {
        if (!$shellCommand) {
            throw $this->createNotFoundException('ShellCommands entity not found.');
        }

        return $this->render('shell_commands/show.html.twig', [
            'shell_command' => $shellCommand,
        ]);
    }

    #[Route('/{id<\d+>}/edit', name: 'app_shell_commands_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ?ShellCommands $shellCommand, EntityManagerInterface $entityManager): Response
    {
        if (!$shellCommand) {
            throw $this->createNotFoundException('ShellCommands entity not found.');
        }

        $form = $this->createForm(ShellCommandsForm::class, $shellCommand);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_shell_commands_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('shell_commands/edit.html.twig', [
            'shell_command' => $shellCommand,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_shell_commands_delete', methods: ['POST'])]
    public function delete(Request $request, ?ShellCommands $shellCommand, EntityManagerInterface $entityManager): Response
    {
        if (!$shellCommand) {
            throw $this->createNotFoundException('ShellCommands entity not found.');
        }

        if ($this->isCsrfTokenValid('delete'.$shellCommand->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($shellCommand);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_shell_commands_index', [], Response::HTTP_SEE_OTHER);
    }
    #[Route('/import', name: 'app_shell_commands_import', methods: ['POST'])]
public function import(
    Request $request,
    EntityManagerInterface $em,
    ShellCommandsRepository $repo
): JsonResponse {
    $data = json_decode($request->getContent(), true);

    if (!$data || !is_array($data)) {
        return new JsonResponse(['status' => 'error', 'message' => 'Invalid or empty JSON'], 400);
    }

    $command = new ShellCommands();

    $command->setCommandShort($data['commandShort'] ?? null);
    $command->setCommandFull($data['commandFull'] ?? null);
    $command->setCommandDescription($data['commandDescription'] ?? null);
    $command->setCommandCategory($data['commandCategory'] ?? null);

    if (!empty($data['commandCreateDate'])) {
        try {
            $date = new \DateTimeImmutable($data['commandCreateDate']);
            $command->setCommandCreateDate($date);
        } catch (\Exception $e) {
            return new JsonResponse(['status' => 'error', 'message' => 'Invalid date format'], 400);
        }
    }

    if (!empty($data['commandUser'])) {
        $user = $em->getRepository(User::class)->find($data['commandUser']);
        if ($user) {
            $command->setCommandUser($user);
        }
    }

    $em->persist($command);
    $em->flush();

    return new JsonResponse([
        'status' => 'success',
        'message' => 'ShellCommand imported successfully',
        'id' => $command->getId(),
    ]);
}
}
