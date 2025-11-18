<?php

namespace App\Controller;

use App\Entity\CustomerUser;
use App\Form\CustomerUserType;
use App\Repository\CustomerUserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/customer/user')]
final class CustomerUserController extends AbstractController
{
    #[Route(name: 'app_customer_user_index', methods: ['GET'])]
    public function index(CustomerUserRepository $customerUserRepository): Response
    {
        return $this->render('customer_user/index.html.twig', [
            'customer_users' => $customerUserRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_customer_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $customerUser = new CustomerUser();
        $form = $this->createForm(CustomerUserType::class, $customerUser);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($customerUser);
            $entityManager->flush();

            return $this->redirectToRoute('app_customer_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('customer_user/new.html.twig', [
            'customer_user' => $customerUser,
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_customer_user_show', methods: ['GET'])]
    public function show(CustomerUser $customerUser): Response
    {
        return $this->render('customer_user/show.html.twig', [
            'customer_user' => $customerUser,
        ]);
    }

    #[Route('/{id<\d+>}/edit', name: 'app_customer_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, CustomerUser $customerUser, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CustomerUserType::class, $customerUser);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_customer_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('customer_user/edit.html.twig', [
            'customer_user' => $customerUser,
            'form' => $form,
        ]);
    }

    #[Route('/{id<\d+>}', name: 'app_customer_user_delete', methods: ['POST'])]
    public function delete(Request $request, CustomerUser $customerUser, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$customerUser->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($customerUser);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_customer_user_index', [], Response::HTTP_SEE_OTHER);
    }
}
