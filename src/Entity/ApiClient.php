<?php

namespace App\Entity;

use App\Repository\ApiClientRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\PartnerCompany;

#[ORM\Entity(repositoryClass: ApiClientRepository::class)]
class ApiClient
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

   #[ORM\ManyToOne(targetEntity: PartnerCompany::class)]
private ?PartnerCompany $partnerCompany = null;

    #[ORM\Column]
    private string $passkey; // Hash gespeichert (z. B. bcrypt)

    #[ORM\Column(type: 'json')]
    private array $ipWhitelist = [];

    #[ORM\Column(unique: true)]
    private string $authKey; // wird per API später übergeben

    #[ORM\Column(type: 'boolean')]
    private bool $isValid = false; // kann nur Admin freischalten

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTime $expires = null;

    #[ORM\Column(type: 'string', unique: true)]
    private string $registerToken; // nur temporär, wird nach erfolgreicher Registrierung gelöscht

    #[ORM\Column(type: 'datetime')]
    private \DateTime $createdAt;
    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $apiOwnerName = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $contactEmail = null;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    private ?string $contactPhone = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getPasskey(): ?string
    {
        return $this->passkey;
    }

    public function setPasskey(string $passkey): static
    {
        $this->passkey = $passkey;

        return $this;
    }

    public function getIpWhitelist(): array
    {
        return $this->ipWhitelist;
    }

    public function setIpWhitelist(array $ipWhitelist): static
    {
        $this->ipWhitelist = $ipWhitelist;

        return $this;
    }

    public function getAuthKey(): ?string
    {
        return $this->authKey;
    }

    public function setAuthKey(string $authKey): static
    {
        $this->authKey = $authKey;

        return $this;
    }

    public function isValid(): ?bool
    {
        return $this->isValid;
    }

    public function setIsValid(bool $isValid): static
    {
        $this->isValid = $isValid;

        return $this;
    }

    public function getExpires(): ?\DateTime
    {
        return $this->expires;
    }

    public function setExpires(?\DateTime $expires): static
    {
        $this->expires = $expires;

        return $this;
    }

    public function getRegisterToken(): ?string
    {
        return $this->registerToken;
    }

    public function setRegisterToken(string $registerToken): static
    {
        $this->registerToken = $registerToken;

        return $this;
    }

    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTime $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getApiOwnerName(): ?string
    {
        return $this->apiOwnerName;
    }

    public function setApiOwnerName(?string $apiOwnerName): static
    {
        $this->apiOwnerName = $apiOwnerName;
        return $this;
    }

    public function getContactEmail(): ?string
    {
        return $this->contactEmail;
    }

    public function setContactEmail(?string $contactEmail): static
    {
        $this->contactEmail = $contactEmail;
        return $this;
    }

    public function getContactPhone(): ?string
    {
        return $this->contactPhone;
    }

    public function setContactPhone(?string $contactPhone): static
    {
        $this->contactPhone = $contactPhone;
        return $this;
    }
}

