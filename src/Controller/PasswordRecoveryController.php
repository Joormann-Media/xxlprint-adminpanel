<?php

// src/Controller/PasswordRecoveryController.php
namespace App\Controller;

use App\Entity\User;
use App\Form\ResetPasswordPinType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class PasswordRecoveryController extends AbstractController
{
    #[Route('/account/recovery', name: 'app_user_recovery')]
    public function selfRecovery(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $entityManager
    ): Response {
        $user = $this->getUser();
        if (!$user instanceof User) {
            throw $this->createAccessDeniedException();
        }

        $form = $this->createForm(ResetPasswordPinType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('plainPassword')->getData();
            $plainPin = $form->get('userpin')->getData();

            $user->setPassword($passwordHasher->hashPassword($user, $plainPassword));
            $user->setUserPin($passwordHasher->hashPassword($user, $plainPin));

            $entityManager->flush();
            $this->addFlash('success', 'Passwort und PIN wurden geändert!');
            return $this->redirectToRoute('app_home');
        }

        return $this->render('password_recovery/recover.html.twig', [
            'form' => $form->createView(),
            'page_title' => 'Passwort/PIN ändern',
        ]);
    }

    #[Route('/admin/user/{id<\d+>}/recovery', name: 'app_admin_user_recovery')]
    #[IsGranted('ROLE_ADMIN')]
    public function adminRecovery(
        User $user,
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $entityManager
    ): Response {
        $form = $this->createForm(ResetPasswordPinType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('plainPassword')->getData();
            $plainPin = $form->get('userpin')->getData();

            $user->setPassword($passwordHasher->hashPassword($user, $plainPassword));
            $user->setUserPin($passwordHasher->hashPassword($user, $plainPin));

            $entityManager->flush();
            $this->addFlash('success', 'Passwort und PIN für den User wurden geändert!');
            return $this->redirectToRoute('app_user_index');
        }

        return $this->render('password_recovery/admin_reset.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
            'page_title' => 'User Passwort/PIN zurücksetzen',
        ]);
    }
}

