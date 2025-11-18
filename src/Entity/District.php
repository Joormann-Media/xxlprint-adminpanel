<?php

namespace App\Entity;

use App\Repository\DistrictRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\City;
use App\Entity\OfficialAddresses;

#[ORM\Entity(repositoryClass: DistrictRepository::class)]
#[ORM\Table(name: 'districts')]
class District
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(length: 150)]
    private ?string $name = null;

    #[ORM\ManyToOne(targetEntity: City::class, inversedBy: 'districts')]
    #[ORM\JoinColumn(nullable: false)]
    private ?City $city = null;

    #[ORM\OneToMany(mappedBy: 'district', targetEntity: OfficialAddresses::class)]
    private Collection $officialAddresses;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private ?string $type = null; // z. B. Ortsteil, Bezirk, Viertel, Gemarkung

    #[ORM\Column(type: 'boolean', options: ['default' => true])]
    private bool $isActive = true;

    public function __construct()
    {
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

    public function getCity(): ?City
    {
        return $this->city;
    }

    public function setCity(?City $city): static
    {
        $this->city = $city;
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

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): static
    {
        $this->type = $type;
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
            $address->setDistrict($this);
        }

        return $this;
    }

    public function removeOfficialAddress(OfficialAddresses $address): static
    {
        if ($this->officialAddresses->removeElement($address)) {
            if ($address->getDistrict() === $this) {
                $address->setDistrict(null);
            }
        }

        return $this;
    }
}
