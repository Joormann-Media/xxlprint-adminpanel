<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\TokenService;
use App\Service\MailService;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

class ResetController extends AbstractController
{
    #[Route('/request-reset-pin', name: 'request_reset_pin', methods: ['GET', 'POST'])]
    public function requestPinReset(
        Request $request,
        UserRepository $userRepo,
        TokenService $tokenService,
        MailService $mailService,
        CsrfTokenManagerInterface $csrfTokenManager
    ): Response {
        if ($request->isMethod('POST')) {
            $email = $request->request->get('email');
            $submittedToken = $request->request->get('_csrf_token');

            if (!$csrfTokenManager->isTokenValid(new \Symfony\Component\Security\Csrf\CsrfToken('request_pin_reset', $submittedToken))) {
                $this->addFlash('danger', 'Ungültiges CSRF-Token.');
                return $this->redirectToRoute('request_reset_pin');
            }

            if ($email) {
                $user = $userRepo->findOneBy(['email' => $email]);

                if ($user) {
                    $token = $tokenService->generateToken($user, 'pin_reset', 60);
                    $mailService->sendPinResetEmail($user, $token->getToken());

                    $this->addFlash('success', 'Ein Link zum Zurücksetzen deiner PIN wurde per E-Mail verschickt.');
                    return $this->redirectToRoute('app_login');
                }

                $this->addFlash('danger', 'Diese E-Mail ist nicht bekannt.');
            } else {
                $this->addFlash('warning', 'Bitte gib eine E-Mail-Adresse ein.');
            }
        }

        return $this->render('token/request_pin.html.twig', [
            'csrf_token' => $csrfTokenManager->getToken('request_pin_reset')->getValue(),
            'page_title' => 'PIN zurücksetzen',
            'page_description' => 'Gib deine E-Mail-Adresse ein, um einen Link zum Zurücksetzen deiner PIN zu erhalten.',
        ]);
    }

    #[Route('/request-reset-password', name: 'request_reset_password', methods: ['GET', 'POST'])]
    public function requestPasswordReset(
        Request $request,
        UserRepository $userRepo,
        TokenService $tokenService,
        MailService $mailService,
        CsrfTokenManagerInterface $csrfTokenManager
    ): Response {
        if ($request->isMethod('POST')) {
            $email = $request->request->get('email');
            $submittedToken = $request->request->get('_csrf_token');

            if (!$csrfTokenManager->isTokenValid(new \Symfony\Component\Security\Csrf\CsrfToken('request_password_reset', $submittedToken))) {
                $this->addFlash('danger', 'Ungültiges CSRF-Token.');
                return $this->redirectToRoute('request_reset_password');
            }

            if ($email) {
                $user = $userRepo->findOneBy(['email' => $email]);

                if ($user) {
                    $token = $tokenService->generateToken($user, 'password_reset', 60);
                    $mailService->sendPasswordResetEmail($user, $token->getToken());

                    $this->addFlash('success', 'Ein Link zum Zurücksetzen deines Passworts wurde per E-Mail verschickt.');
                    return $this->redirectToRoute('app_login');
                }

                $this->addFlash('danger', 'Diese E-Mail ist nicht bekannt.');
            } else {
                $this->addFlash('warning', 'Bitte gib eine E-Mail-Adresse ein.');
            }
        }

        return $this->render('token/request_password.html.twig', [
            'csrf_token' => $csrfTokenManager->getToken('request_password_reset')->getValue(),
            'page_title' => 'Passwort zurücksetzen',
            'page_description' => 'Gib deine E-Mail-Adresse ein, um einen Link zum Zurücksetzen deines Passworts zu erhalten.',
        ]);
    }
}
