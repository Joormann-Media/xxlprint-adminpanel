<?php

namespace App\Entity;

use App\Repository\PostalCodeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PostalCodeRepository::class)]
#[ORM\Table(name: 'postal_codes')]
class PostalCode
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(length: 5)]
    private ?string $postcode = null;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private ?bool $valid = null;

    #[ORM\OneToMany(mappedBy: 'postalCode', targetEntity: Street::class)]
    private Collection $streets;

    public function __construct()
    {
        $this->streets = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPostcode(): ?string
    {
        return $this->postcode;
    }

    public function setPostcode(string $postcode): static
    {
        $this->postcode = $postcode;
        return $this;
    }

    public function isValid(): ?bool
    {
        return $this->valid;
    }

    public function setValid(?bool $valid): static
    {
        $this->valid = $valid;
        return $this;
    }

    /**
     * @return Collection<int, Street>
     */
    public function getStreets(): Collection
    {
        return $this->streets;
    }

    public function addStreet(Street $street): static
    {
        if (!$this->streets->contains($street)) {
            $this->streets->add($street);
            $street->setPostalCode($this);
        }

        return $this;
    }

    public function removeStreet(Street $street): static
    {
        if ($this->streets->removeElement($street)) {
            if ($street->getPostalCode() === $this) {
                $street->setPostalCode(null);
            }
        }

        return $this;
    }
}
