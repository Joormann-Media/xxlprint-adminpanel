<?php

namespace App\Security;

use App\Repository\UserRepository;
use App\Repository\UserHistoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpFoundation\Request as HttpRequest;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class CustomerLoginAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'app_login';

    public function __construct(
        private UrlGeneratorInterface $urlGenerator,
        private UserRepository $userRepository,
        private EntityManagerInterface $entityManager,
        private UserHistoryRepository $userHistoryRepository,
        private HttpKernelInterface $httpKernel,
        private TokenStorageInterface $tokenStorage // 🔐 Damit SubRequest den User kennt
    ) {}

    public function authenticate(Request $request): Passport
    {
        $identifier = $request->request->get('username');
        $password = $request->request->get('password');
        $request->getSession()->set(Security::LAST_USERNAME, $identifier);

        return new Passport(
            new UserBadge($identifier, fn ($userIdentifier) => $this->userRepository->loadUserByIdentifier($userIdentifier)),
            new PasswordCredentials($password),
            [
                new CsrfTokenBadge('authenticate', $request->request->get('_csrf_token')),
                new RememberMeBadge(),
            ]
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
{
    /** @var User $user */
    $user = $token->getUser();

    if ($user instanceof User) {
        $user->setLastlogindate(new \DateTimeImmutable()); // 🔥 Wichtig: Immutable oder Mutable wie dein Mapping!
        $this->entityManager->flush(); // ✅ Sollte jetzt korrekt gespeichert werden
    }

    if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
        return new RedirectResponse($targetPath);
    }

    return new RedirectResponse($this->urlGenerator->generate('app_home'));
}


    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }
}
