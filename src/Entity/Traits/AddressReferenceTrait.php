<?php

namespace App\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

trait AddressReferenceTrait
{
    #[ORM\ManyToOne(targetEntity: \App\Entity\PostalCode::class)]
    #[ORM\JoinColumn(nullable: true)]
    private ?\App\Entity\PostalCode $postalCode = null;

    #[ORM\ManyToOne(targetEntity: \App\Entity\Street::class)]
    #[ORM\JoinColumn(nullable: true)]
    private ?\App\Entity\Street $street = null;

    #[ORM\Column(type: 'string', length: 20, nullable: true)]
    private ?string $houseNumber = null;

    public function getPostalCode(): ?\App\Entity\PostalCode
    {
        return $this->postalCode;
    }

    public function setPostalCode(?\App\Entity\PostalCode $postalCode): self
    {
        $this->postalCode = $postalCode;
        return $this;
    }

    public function getStreet(): ?\App\Entity\Street
    {
        return $this->street;
    }

    public function setStreet(?\App\Entity\Street $street): self
    {
        $this->street = $street;
        return $this;
    }

    public function getHouseNumber(): ?string
    {
        return $this->houseNumber;
    }

    public function setHouseNumber(?string $houseNumber): self
    {
        $this->houseNumber = $houseNumber;
        return $this;
    }

    /**
     * Gibt eine formatierte Adresse zurück.
     *
     * @param string $mode short | default | extended
     */
    public function getFullAddress(string $mode = 'default'): string
    {
        $parts = [];

        // Straße + Hausnummer (immer)
        if ($this->street?->getName()) {
            $parts[] = $this->street->getName();
        }

        if ($this->houseNumber) {
            $parts[] = $this->houseNumber;
        }

        // Nur bei "default" oder "extended"
        if (in_array($mode, ['default', 'extended']) && $this->postalCode) {
            $parts[] = trim($this->postalCode->getPostcode() . ' ' . $this->postalCode->getCity());
        }

        // Nur bei "extended"
        if ($mode === 'extended') {
            if ($this->postalCode?->getDistrict()) {
                $parts[] = $this->postalCode->getDistrict();
            }
            if ($this->postalCode?->getState()) {
                $parts[] = $this->postalCode->getState();
            }
            if ($this->postalCode?->getCountry()) {
                $parts[] = $this->postalCode->getCountry();
            }
        }

        return implode(', ', array_filter($parts));
    }
}
