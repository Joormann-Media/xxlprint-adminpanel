<?php

namespace App\Controller;

use App\Entity\ApiClient;
use App\Entity\ApiToken;
use App\Form\ApiClientType;
use App\Repository\ApiClientRepository;
use App\Repository\ApiTokenRepository;
use App\Repository\PartnerCompanyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api-client')]
final class ApiClientController extends AbstractController
{
    #[Route(name: 'app_api_client_index', methods: ['GET'])]
    public function index(ApiClientRepository $apiClientRepository): Response
    {
        return $this->render('api_client/index.html.twig', [
            'api_clients' => $apiClientRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_api_client_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $apiClient = new ApiClient();
        $form = $this->createForm(ApiClientType::class, $apiClient);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($apiClient);
            $entityManager->flush();

            return $this->redirectToRoute('app_api_client_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('api_client/new.html.twig', [
            'api_client' => $apiClient,
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_api_client_show', methods: ['GET'])]
    public function show(ApiClient $apiClient): Response
    {
        return $this->render('api_client/show.html.twig', [
            'api_client' => $apiClient,
        ]);
    }

    #[Route('/{id<\d+>}/edit', name: 'app_api_client_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ApiClient $apiClient, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ApiClientType::class, $apiClient);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_api_client_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('api_client/edit.html.twig', [
            'api_client' => $apiClient,
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_api_client_delete', methods: ['POST'])]
    public function delete(Request $request, ApiClient $apiClient, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$apiClient->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($apiClient);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_api_client_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/register/{token}', name: 'api_client_register', methods: ['GET', 'POST'])]
    public function registerWithToken(
        string $token,
        Request $request,
        ApiTokenRepository $tokenRepo,
        PartnerCompanyRepository $partnerRepo,
        EntityManagerInterface $em
    ): Response {
        // 🔍 Token validieren
        $apiToken = $tokenRepo->findOneBy(['token' => $token]);
        if (
            !$apiToken ||
            $apiToken->isUsed() ||
            $apiToken->getExpiresAt() < new \DateTime()
        ) {
            throw $this->createNotFoundException('Ungültiges oder abgelaufenes Token.');
        }

        // 🎩 Neuen Client vorbereiten
        $client = new ApiClient();
        $client->setRegisterToken($token);
        $client->setPartnerCompany($apiToken->getPartnerCompany());
        $client->setAuthKey(bin2hex(random_bytes(16)));
        $client->setPasskey(password_hash(bin2hex(random_bytes(16)), PASSWORD_BCRYPT));
        $client->setCreatedAt(new \DateTime());
        $client->setExpires(new \DateTime('2999-12-31'));
        $client->setIsValid(false); // Muss später freigegeben werden

        $form = $this->createForm(ApiClientType::class, $client);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Token als "verbraucht" markieren
            $apiToken->setUsed(true);
            $em->persist($client);
            $em->flush();

            return $this->redirectToRoute('app_api_client_index');
        }

        return $this->render('api_client/register.html.twig', [
            'form' => $form,
            'api_client' => $client,
            'token_info' => $apiToken,
        ]);
    }
}
