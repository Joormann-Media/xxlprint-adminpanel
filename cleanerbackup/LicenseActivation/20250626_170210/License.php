<?php

namespace App\Entity;

use App\Repository\LicenseRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LicenseRepository::class)]
class License
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'string', length: 255)]
    private string $productName;

    #[ORM\Column(type: 'string', length: 255, unique: true)]
    private string $licenseKey;

    #[ORM\Column(type: 'string', length: 50)]
    private string $version;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $validUntil;

    #[ORM\Column(type: 'integer')]
    private int $maxActivations = 1;

    #[ORM\Column(type: 'string', length: 20)]
    private string $status = 'active';

    #[ORM\ManyToOne(targetEntity: Customer::class, inversedBy: 'licenses')]
    #[ORM\JoinColumn(nullable: false)]
    private Customer $customer;

    #[ORM\OneToMany(mappedBy: 'license', targetEntity: LicenseActivation::class, cascade: ['persist'], orphanRemoval: true)]
    private Collection $activations;

    public function __construct()
    {
        $this->activations = new ArrayCollection();
    }

    public function getId(): int { return $this->id; }
    public function getProductName(): string { return $this->productName; }
    public function setProductName(string $productName): self { $this->productName = $productName; return $this; }
    public function getLicenseKey(): string { return $this->licenseKey; }
    public function setLicenseKey(string $licenseKey): self { $this->licenseKey = $licenseKey; return $this; }
    public function getVersion(): string { return $this->version; }
    public function setVersion(string $version): self { $this->version = $version; return $this; }
    public function getValidUntil(): \DateTimeInterface { return $this->validUntil; }
    public function setValidUntil(\DateTimeInterface $validUntil): self { $this->validUntil = $validUntil; return $this; }
    public function getMaxActivations(): int { return $this->maxActivations; }
    public function setMaxActivations(int $maxActivations): self { $this->maxActivations = $maxActivations; return $this; }
    public function getStatus(): string { return $this->status; }
    public function setStatus(string $status): self { $this->status = $status; return $this; }
    public function getCustomer(): Customer { return $this->customer; }
    public function setCustomer(Customer $customer): self { $this->customer = $customer; return $this; }

    public function getActivations(): Collection { return $this->activations; }
}