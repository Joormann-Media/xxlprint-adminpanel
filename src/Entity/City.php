<?php

namespace App\Entity;

use App\Repository\CityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CityRepository::class)]
#[ORM\Table(name: 'cities')]
class City
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(length: 150)]
    private ?string $name = null;

    #[ORM\Column(length: 150, nullable: true)]
    private ?string $state = null;

    #[ORM\Column(length: 150, nullable: true)]
    private ?string $country = null;

    #[ORM\Column(type: 'boolean', options: ['default' => true])]
    private bool $isActive = true;

    #[ORM\OneToMany(mappedBy: 'city', targetEntity: District::class)]
    private Collection $districts;

    #[ORM\OneToMany(mappedBy: 'city', targetEntity: OfficialAddresses::class)]
    private Collection $officialAddresses;

    public function __construct()
    {
        $this->districts = new ArrayCollection();
        $this->officialAddresses = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;
        return $this;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(?string $state): static
    {
        $this->state = $state;
        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(?string $country): static
    {
        $this->country = $country;
        return $this;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): static
    {
        $this->isActive = $isActive;
        return $this;
    }

    /**
     * @return Collection<int, District>
     */
    public function getDistricts(): Collection
    {
        return $this->districts;
    }

    public function addDistrict(District $district): static
    {
        if (!$this->districts->contains($district)) {
            $this->districts->add($district);
            $district->setCity($this);
        }

        return $this;
    }

    public function removeDistrict(District $district): static
    {
        if ($this->districts->removeElement($district)) {
            if ($district->getCity() === $this) {
                $district->setCity(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, OfficialAddresses>
     */
    public function getOfficialAddresses(): Collection
    {
        return $this->officialAddresses;
    }

    public function addOfficialAddress(OfficialAddresses $address): static
    {
        if (!$this->officialAddresses->contains($address)) {
            $this->officialAddresses->add($address);
            $address->setCity($this);
        }

        return $this;
    }

    public function removeOfficialAddress(OfficialAddresses $address): static
    {
        if ($this->officialAddresses->removeElement($address)) {
            if ($address->getCity() === $this) {
                $address->setCity(null);
            }
        }

        return $this;
    }
}
