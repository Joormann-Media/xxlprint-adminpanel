<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Contracts\Service\Attribute\Required;

class MobileVerificationService
{
    private SessionInterface $session;
    private readonly string $codeSessionKey;

    #[Required]
    public function setSession(SessionInterface $session): void
    {
        $this->session = $session;
        $this->codeSessionKey = '_mobile_verification_code';
    }

    public function generateAndStoreCode(): string
    {
        $code = random_int(100000, 999999);
        $this->session->set($this->codeSessionKey, $code);
        return (string) $code;
    }

    public function validateCode(string $input): bool
    {
        $stored = $this->session->get($this->codeSessionKey);
        return $stored && $input === (string) $stored;
    }

    public function clearCode(): void
    {
        $this->session->remove($this->codeSessionKey);
    }
}

