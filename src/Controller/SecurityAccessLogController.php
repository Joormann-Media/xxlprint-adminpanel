<?php

namespace App\Controller;

use App\Entity\SecurityAccessLog;
use App\Form\SecurityAccessLogType;
use App\Repository\SecurityAccessLogRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/security-access/log')]
final class SecurityAccessLogController extends AbstractController
{
    #[Route(name: 'app_security_access_log_index', methods: ['GET'])]
    public function index(SecurityAccessLogRepository $securityAccessLogRepository): Response
    {
        return $this->render('security_access_log/index.html.twig', [
            'security_access_logs' => $securityAccessLogRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_security_access_log_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $securityAccessLog = new SecurityAccessLog();
        $form = $this->createForm(SecurityAccessLogType::class, $securityAccessLog);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($securityAccessLog);
            $entityManager->flush();

            return $this->redirectToRoute('app_security_access_log_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('security_access_log/new.html.twig', [
            'security_access_log' => $securityAccessLog,
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_security_access_log_show', methods: ['GET'])]
    public function show(SecurityAccessLog $securityAccessLog): Response
    {
        return $this->render('security_access_log/show.html.twig', [
            'security_access_log' => $securityAccessLog,
        ]);
    }

    #[Route('/{id<\d+>}/edit', name: 'app_security_access_log_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, SecurityAccessLog $securityAccessLog, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(SecurityAccessLogType::class, $securityAccessLog);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_security_access_log_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('security_access_log/edit.html.twig', [
            'security_access_log' => $securityAccessLog,
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_security_access_log_delete', methods: ['POST'])]
    public function delete(Request $request, SecurityAccessLog $securityAccessLog, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$securityAccessLog->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($securityAccessLog);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_security_access_log_index', [], Response::HTTP_SEE_OTHER);
    }
    #[Route('/import', name: 'security_access_log_import', methods: ['POST'])]
public function import(
    Request $request,
    EntityManagerInterface $em,
    SerializerInterface $serializer
): JsonResponse {
    $data = json_decode($request->getContent(), true);

    if (!$data) {
        return new JsonResponse(['error' => 'Invalid JSON'], 400);
    }

    // Hydriere das Entity über den Serializer!
    $log = $serializer->denormalize($data, SecurityAccessLog::class);

    $em->persist($log);
    $em->flush();

    return new JsonResponse(['status' => 'ok', 'id' => $log->getId()]);
}
#[Route('/bulk-delete', name: 'security_access_log_bulk_delete', methods: ['POST'])]
public function bulkDelete(
    Request $request,
    EntityManagerInterface $em,
    SecurityAccessLogRepository $repo
): JsonResponse {
    $data = json_decode($request->getContent(), true);

    if (!$data || empty($data['ids']) || !is_array($data['ids'])) {
        return new JsonResponse(['status' => 'error', 'message' => 'IDs fehlen oder ungültig!'], 400);
    }

    $ids = array_map('intval', $data['ids']);
    $logs = $repo->createQueryBuilder('l')
        ->where('l.id IN (:ids)')
        ->setParameter('ids', $ids)
        ->getQuery()->getResult();

    $count = 0;
    foreach ($logs as $log) {
        $em->remove($log);
        $count++;
    }
    $em->flush();

    return new JsonResponse([
        'status' => 'ok',
        'deleted' => $count,
        'ids' => $ids
    ]);
}

}
