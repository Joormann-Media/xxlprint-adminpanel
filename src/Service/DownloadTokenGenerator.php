<?php

namespace App\Service;

use App\Entity\DownloadToken;
use App\Entity\ReleaseFile;
use Symfony\Component\Uid\Uuid;

class DownloadTokenGenerator
{
    public function generate(ReleaseFile $file, int $validMinutes = 10, ?string $ip = null): DownloadToken
{
    $token = new DownloadToken();
    $token->setToken(Uuid::v4()->toRfc4122());
    $token->setExpiresAt((new \DateTime())->modify("+$validMinutes minutes"));
    $token->setReleaseFile($file);
    $token->setUsed(false);

    if ($ip) {
        $token->setIp($ip); // 💡 falls deine Entity `DownloadToken` ein Feld `ip` hat
    }

    return $token;
}

}
