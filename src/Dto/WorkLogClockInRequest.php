<?php
// src/Dto/WorkLogClockInRequest.php
namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class WorkLogClockInRequest
{
    #[Assert\NotBlank]
    public int $employeeNumber;

    #[Assert\NotBlank]
    #[Assert\Type("string")]
    public string $location;

    #[Assert\NotBlank]
    #[Assert\Choice(['app', 'web', 'card', 'nfc', 'qrcode', 'manual'])]
    public string $method;

    #[Assert\NotBlank]
    #[Assert\Type("string")]
    public string $deviceUid;

    #[Assert\Type("string")]
    public ?string $source = null;
}

