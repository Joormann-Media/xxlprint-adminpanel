<?php

// src/Security/SabreBasicAuthenticator.php

namespace App\Security;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;

// **Wichtig:**
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class SabreBasicAuthenticator extends AbstractAuthenticator
{
    private UserProviderInterface $userProvider;
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserProviderInterface $userProvider, UserPasswordHasherInterface $passwordHasher)
    {
        $this->userProvider = $userProvider;
        $this->passwordHasher = $passwordHasher;
    }

    public function supports(Request $request): bool
    {
        // Nur Basic Auth für /dav und nur, wenn Authorization Header mit Basic vorhanden ist
        return str_starts_with($request->getPathInfo(), '/dav') &&
               $request->headers->has('Authorization') &&
               str_starts_with($request->headers->get('Authorization'), 'Basic ');
    }

    public function authenticate(Request $request): Passport
{
    $auth = $request->headers->get('Authorization');
    $encoded = substr($auth, 6);
    $decoded = base64_decode($encoded);
    if (!$decoded || !str_contains($decoded, ':')) {
        throw new CustomUserMessageAuthenticationException('Invalid Basic authentication header');
    }

    [$username, $password] = explode(':', $decoded, 2);

    return new Passport(
        new UserBadge($username, function ($userIdentifier) {
            return $this->userProvider->loadUserByIdentifier($userIdentifier);
        }),
        new PasswordCredentials($password)
    );
}

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        // Auth erfolgreich, nichts tun, Anfrage normal weiterleiten
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        // 401 zurückgeben, damit Client (Thunderbird) nach Login fragt
        return new Response('Authentication Failed: ' . $exception->getMessage(), 401, [
            'WWW-Authenticate' => 'Basic realm="SabreDAV"',
        ]);
    }
}
