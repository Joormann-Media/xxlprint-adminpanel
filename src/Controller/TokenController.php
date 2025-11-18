<?php

namespace App\Controller;

use App\Form\ResetPinType;
use App\Form\ResetPasswordType;
use Symfony\Component\HttpFoundation\JsonResponse;

use App\Entity\UserToken;
use App\Service\TokenService;
use App\Repository\UserTokenRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class TokenController extends AbstractController
{
    #[Route('/verify/{token}', name: 'token_verify')]
    public function verify(string $token, UserTokenRepository $tokenRepo, EntityManagerInterface $em): Response
    {
        $userToken = $tokenRepo->findOneBy(['token' => $token, 'type' => 'verify', 'used' => false]);

        if (!$userToken || $userToken->getExpiresAt() < new \DateTime()) {
            return $this->render('token/invalid.html.twig');
        }

        $user = $userToken->getUser();
        $user->setIsVerified(true);
        $userToken->setUsed(true);

        $em->flush();

        return $this->render('token/verified.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/reset-password/{token}', name: 'token_reset_password')]
    public function resetPassword(
        string $token,
        Request $request,
        UserTokenRepository $tokenRepo,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $hasher
    ): Response {
        $userToken = $tokenRepo->findOneBy(['token' => $token, 'type' => 'password_reset', 'used' => false]);

        if (!$userToken || $userToken->getExpiresAt() < new \DateTime()) {
            return $this->render('token/invalid.html.twig');
        }

        /** @var User $user */
        $user = $userToken->getUser();

        $form = $this->createForm(ResetPasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('password')->getData();

            $user->setPassword($hasher->hashPassword($user, $plainPassword));
            $userToken->setUsed(true);
            $em->flush();

            $this->addFlash('success', 'Dein Passwort wurde erfolgreich zurückgesetzt.');

            return $this->redirectToRoute('app_login');
        }

        return $this->render('token/reset_password.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
        ]);
    }

    

    #[Route('/reset-pin/{token}', name: 'token_reset_pin')]
    public function resetPin(
        string $token,
        Request $request,
        UserTokenRepository $tokenRepo,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $hasher
    ): Response {
        $userToken = $tokenRepo->findOneBy([
            'token' => $token,
            'type' => 'pin_reset',
            'used' => false,
        ]);
    
        if (!$userToken || $userToken->getExpiresAt() < new \DateTime()) {
            return $this->render('token/invalid.html.twig');
        }
    
        $form = $this->createForm(ResetPinType::class);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $userToken->getUser();
            $plainPin = $form->get('pin')->getData();
    
            $user->setUserPin(
                $hasher->hashPassword($user, $plainPin)
            );
            $userToken->setUsed(true);
    
            $em->flush();
    
            return $this->render('token/pin_reset_success.html.twig', [
                'user' => $user,
            ]);
        }
    
        return $this->render('token/reset_pin.html.twig', [
            'form' => $form->createView(),
            'token' => $token,
        ]);
    }
    

    #[Route('/consume/{token}', name: 'token_consume')]
    public function consumeGeneric(string $token, UserTokenRepository $tokenRepo, EntityManagerInterface $em): Response
    {
        $userToken = $tokenRepo->findOneBy(['token' => $token, 'used' => false]);

        if (!$userToken || $userToken->getExpiresAt() < new \DateTime()) {
            return $this->render('token/invalid.html.twig');
        }

        $userToken->setUsed(true);
        $em->flush();

        return $this->redirectToRoute('dashboard');
    }
}
