<?php
namespace App\Security;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class EmailOrUsernameUserProvider implements UserProviderInterface
{
    public function __construct(
        private UserRepository $userRepository
    ) {}

    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        $user = $this->userRepository->findOneBy(['email' => $identifier])
             ?? $this->userRepository->findOneBy(['username' => $identifier])
             ?? $this->userRepository->findOneBy(['customerId' => $identifier])
             ?? $this->userRepository->findOneBy(['mobile' => $identifier]);

            // Mobilnummer nur prüfen, wenn das Eingabefeld aussieht wie eine Nummer
    if (!$user && preg_match('/^\+?\d{5,}$/', $identifier)) {
        $normalized = $this->normalizeMobile($identifier);
        $user = $this->userRepository->findOneBy([
            'mobile' => $normalized,
            'mobileVerified' => true,
        ]);
        
    }
             

        if (!$user) {
            throw new UserNotFoundException("Benutzer nicht gefunden.");
        }

        return $user;
    }

    public function refreshUser(UserInterface $user): UserInterface
{
    file_put_contents(__DIR__.'/refreshuser.log', date('c').' REFRESH: '.$user->getUserIdentifier()."\n", FILE_APPEND);

    if (!$user instanceof User) {
        throw new \LogicException('Unsupported user class: ' . get_class($user));
    }

    $reloadedUser = $this->userRepository->find($user->getId());

    if (!$reloadedUser) {
        file_put_contents(__DIR__.'/refreshuser.log', date('c').' RELOAD FAILED: id='.$user->getId()."\n", FILE_APPEND);
        throw new UserNotFoundException('Benutzer konnte nicht neu geladen werden.');
    }

    file_put_contents(__DIR__.'/refreshuser.log', date('c').' RELOAD OK: id='.$reloadedUser->getId()."\n", FILE_APPEND);

    return $reloadedUser;
}

    public function supportsClass(string $class): bool
    {
        return $class === User::class;
    }
    private function normalizeMobile(string $mobile): string
{
    // Entfernt Leerzeichen, Klammern, Bindestriche, etc.
    $clean = preg_replace('/[^\d+]/', '', $mobile);

    // Ersetzt 0049 oder 0 am Anfang durch +49
    if (str_starts_with($clean, '0049')) {
        return '+49' . substr($clean, 4);
    } elseif (str_starts_with($clean, '0')) {
        return '+49' . substr($clean, 1);
    }

    return $clean;
}

}

