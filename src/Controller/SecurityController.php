<?php

namespace App\Controller;

use App\Repository\UserSessionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;


class SecurityController extends AbstractController
{
    public function __construct(
        private RequestStack $requestStack,
        private Security $security,
        private EntityManagerInterface $em,
        private UserSessionRepository $userSessionRepository,
    ) {}

    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        // Wenn der Benutzer bereits eingeloggt ist, leite ihn zur Startseite weiter
        if ($this->security->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirectToRoute('app_home');
        }
        // Wenn der Benutzer nicht eingeloggt ist, zeige das Login-Formular an
        // Hier kannst du auch die Login-Formular-Logik hinzufügen, falls erforderlich
        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
            'page_title' => 'Login',
        ]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('Diese Methode wird vom Firewall-Logout überschrieben.');
    }

    #[Route(path: '/logout-success', name: 'app_logout_success')]
    public function logoutSuccess(AuthenticationUtils $authenticationUtils): Response
    {
        $session = $this->requestStack->getSession();
        $sessionId = $session?->getId();
        $user = $this->security->getUser();
        
        // Lösche alle Sessions des Benutzers
        if ($user && method_exists($user, 'getId')) {
            $this->userSessionRepository->deleteByUserId($user->getId());
        }

        // Lösche die aktuelle Session aus der Datenbank
        if ($sessionId) {
            $this->userSessionRepository->deleteBySessionId($sessionId);
        }

        $lastUsername = $authenticationUtils->getLastUsername();
        $this->addFlash('success', 'Logout erfolgreich: ' . $lastUsername);

        return $this->render('security/logout_success.html.twig', [
            'last_username' => $lastUsername,
            'page_title' => 'Logout Erfolgreich',
            'message' => 'Du wurdest erfolgreich ausgeloggt.',
        ]);
    }
}
