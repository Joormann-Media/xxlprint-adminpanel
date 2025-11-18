<?php

namespace App\Entity;

use App\Repository\CustomerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CustomerRepository::class)]
class Customer
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'string', length: 255)]
    private string $name;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $address = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $contactPerson = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $notes = null;

    #[ORM\OneToMany(mappedBy: 'customer', targetEntity: CustomerUser::class, cascade: ['persist'], orphanRemoval: true)]
    private Collection $customerUsers;

    #[ORM\OneToMany(mappedBy: 'customer', targetEntity: License::class, cascade: ['persist'], orphanRemoval: true)]
    private Collection $licenses;

    public function __construct()
    {
        $this->customerUsers = new ArrayCollection();
        $this->licenses = new ArrayCollection();
    }

    public function getId(): int { return $this->id; }
    public function getName(): string { return $this->name; }
    public function setName(string $name): self { $this->name = $name; return $this; }
    public function getAddress(): ?string { return $this->address; }
    public function setAddress(?string $address): self { $this->address = $address; return $this; }
    public function getContactPerson(): ?string { return $this->contactPerson; }
    public function setContactPerson(?string $contactPerson): self { $this->contactPerson = $contactPerson; return $this; }
    public function getNotes(): ?string { return $this->notes; }
    public function setNotes(?string $notes): self { $this->notes = $notes; return $this; }

    public function getCustomerUsers(): Collection { return $this->customerUsers; }
    public function addCustomerUser(CustomerUser $user): self {
        if (!$this->customerUsers->contains($user)) {
            $this->customerUsers[] = $user;
            $user->setCustomer($this);
        }
        return $this;
    }
    public function removeCustomerUser(CustomerUser $user): self {
        if ($this->customerUsers->removeElement($user)) {
            if ($user->getCustomer() === $this) $user->setCustomer(null);
        }
        return $this;
    }

    public function getLicenses(): Collection { return $this->licenses; }
    public function addLicense(License $license): self {
        if (!$this->licenses->contains($license)) {
            $this->licenses[] = $license;
            $license->setCustomer($this);
        }
        return $this;
    }
    public function removeLicense(License $license): self {
        if ($this->licenses->removeElement($license)) {
            if ($license->getCustomer() === $this) $license->setCustomer(null);
        }
        return $this;
    }
}