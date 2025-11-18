<?php

namespace App\Entity;

use App\Repository\ApiTokenRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: ApiTokenRepository::class)]
class ApiToken
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'string', unique: true)]
    private string $token;

    #[ORM\Column(type: 'string', length: 50)]
    private string $type; // z. B. 'api-register', 'external-auth', 'invite'

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $createdAt;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $expiresAt;

    #[ORM\Column(type: 'boolean')]
    private bool $used = false;

    #[ORM\ManyToOne(targetEntity: PartnerCompany::class)]
    private ?PartnerCompany $partnerCompany = null;

    public function __construct()
    {
        // Du kannst alternativ auch: bin2hex(random_bytes(16))
        $this->token = Uuid::v4()->toRfc4122();
        $this->createdAt = new \DateTimeImmutable();
        $this->used = false;

        // Optional: Gültigkeit z. B. für 7 Tage
        $this->expiresAt = (new \DateTimeImmutable())->modify('+7 days');
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(string $token): static
    {
        $this->token = $token;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getExpiresAt(): ?\DateTimeInterface
    {
        return $this->expiresAt;
    }

    public function setExpiresAt(\DateTimeInterface $expiresAt): static
    {
        $this->expiresAt = $expiresAt;

        return $this;
    }

    public function isUsed(): ?bool
    {
        return $this->used;
    }

    public function setUsed(bool $used): static
    {
        $this->used = $used;

        return $this;
    }

    public function getPartnerCompany(): ?PartnerCompany
    {
        return $this->partnerCompany;
    }

    public function setPartnerCompany(?PartnerCompany $partnerCompany): static
    {
        $this->partnerCompany = $partnerCompany;

        return $this;
    }
}
