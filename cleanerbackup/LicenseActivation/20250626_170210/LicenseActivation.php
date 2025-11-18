<?php

namespace App\Entity;

use App\Repository\LicenseActivationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LicenseActivationRepository::class)]
class LicenseActivation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'string', length: 255)]
    private string $hardwareKey;

    #[ORM\Column(type: 'string', length: 255)]
    private string $hostname;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $activatedAt;

    #[ORM\ManyToOne(targetEntity: License::class, inversedBy: 'activations')]
    #[ORM\JoinColumn(nullable: false)]
    private License $license;

    public function getId(): int { return $this->id; }
    public function getHardwareKey(): string { return $this->hardwareKey; }
    public function setHardwareKey(string $hardwareKey): self { $this->hardwareKey = $hardwareKey; return $this; }
    public function getHostname(): string { return $this->hostname; }
    public function setHostname(string $hostname): self { $this->hostname = $hostname; return $this; }
    public function getActivatedAt(): \DateTimeInterface { return $this->activatedAt; }
    public function setActivatedAt(\DateTimeInterface $activatedAt): self { $this->activatedAt = $activatedAt; return $this; }
    public function getLicense(): License { return $this->license; }
    public function setLicense(License $license): self { $this->license = $license; return $this; }
}