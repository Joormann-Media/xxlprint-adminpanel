<?php

namespace App\Controller;

use App\Entity\ApiToken;
use App\Form\ApiTokenType;
use App\Repository\ApiTokenRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Uid\Uuid;


#[Route('/api-token')]
final class ApiTokenController extends AbstractController
{
    #[Route(name: 'app_api_token_index', methods: ['GET'])]
    public function index(ApiTokenRepository $apiTokenRepository): Response
    {
        return $this->render('api_token/index.html.twig', [
            'api_tokens' => $apiTokenRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_api_token_new', methods: ['GET', 'POST'])]
public function new(Request $request, EntityManagerInterface $entityManager): Response
{
    $apiToken = new ApiToken();

    // 🧙‍♀️ Token vorab generieren, wenn leer
    if (!$apiToken->getToken()) {
        $apiToken->setToken(Uuid::v4()->toRfc4122()); // oder: bin2hex(random_bytes(16));
    }

    $form = $this->createForm(ApiTokenType::class, $apiToken);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $entityManager->persist($apiToken);
        $entityManager->flush();

        return $this->redirectToRoute('app_api_token_index', [], Response::HTTP_SEE_OTHER);
    }

    return $this->render('api_token/new.html.twig', [
        'api_token' => $apiToken,
        'form' => $form,
    ]);
}

    #[Route('/{id<\d+>}', name: 'app_api_token_show', methods: ['GET'])]
    public function show(ApiToken $apiToken): Response
    {
        return $this->render('api_token/show.html.twig', [
            'api_token' => $apiToken,
        ]);
    }

    #[Route('/{id<\d+>}/edit', name: 'app_api_token_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ApiToken $apiToken, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ApiTokenType::class, $apiToken);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_api_token_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('api_token/edit.html.twig', [
            'api_token' => $apiToken,
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_api_token_delete', methods: ['POST'])]
    public function delete(Request $request, ApiToken $apiToken, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$apiToken->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($apiToken);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_api_token_index', [], Response::HTTP_SEE_OTHER);
    }
    #[Route('/validate', name: 'api_token_validate', methods: ['POST'])]
public function validateToken(Request $request, ApiTokenRepository $tokenRepo, EntityManagerInterface $entityManager): JsonResponse
{
    $data = json_decode($request->getContent(), true);
    if (!isset($data['token'])) {
        return $this->json(['error' => 'Token fehlt.'], 400);
    }

    $token = $tokenRepo->findOneBy(['token' => $data['token']]);

    if (!$token) {
        return $this->json(['error' => 'Token ungültig.'], 404);
    }

    if ($token->isUsed()) {
        return $this->json(['error' => 'Token wurde bereits verwendet.'], 410);
    }

    if ($token->getExpiresAt() < new \DateTime()) {
        return $this->json(['error' => 'Token ist abgelaufen.'], 410);
    }

    // ✅ Markiere Token als verwendet
    $token->setUsed(true);
    $entityManager->flush();

    // Optional: Infos zum Partner zurückgeben
    $partner = $token->getPartnerCompany();

    return $this->json([
        'success' => true,
        'token' => $token->getToken(),
        'type' => $token->getType(),
        'used' => true,
        'valid_until' => $token->getExpiresAt()->format(DATE_ATOM),
        'partner' => $partner ? [
            'id' => $partner->getId(),
            'name' => $partner->getName(),
        ] : null
    ]);
}


}
