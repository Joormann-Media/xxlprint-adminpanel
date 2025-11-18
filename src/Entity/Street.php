<?php

namespace App\Entity;

use App\Repository\StreetRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StreetRepository::class)]
#[ORM\Table(name: 'streets')]
class Street
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(length: 150)]
    private ?string $name = null;

    #[ORM\ManyToOne(targetEntity: PostalCode::class, inversedBy: 'streets')]
    #[ORM\JoinColumn(nullable: false)]
    private ?PostalCode $postalCode = null;

    #[ORM\ManyToOne(targetEntity: City::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?City $city = null;

    #[ORM\ManyToOne(targetEntity: District::class)]
    #[ORM\JoinColumn(nullable: true)]
    private ?District $district = null;

    #[ORM\Column(type: 'boolean', options: ['default' => true])]
    private bool $isActive = true;

    #[ORM\OneToMany(mappedBy: 'street', targetEntity: HouseNumber::class)]
    private Collection $houseNumbers;

    #[ORM\OneToMany(mappedBy: 'street', targetEntity: OfficialAddresses::class)]
    private Collection $officialAddresses;

    public function __construct()
    {
        $this->houseNumbers = new ArrayCollection();
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

    public function isActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): static
    {
        $this->isActive = $isActive;

        return $this;
    }

    public function getPostalCode(): ?PostalCode
    {
        return $this->postalCode;
    }

    public function setPostalCode(?PostalCode $postalCode): static
    {
        $this->postalCode = $postalCode;

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

    public function getDistrict(): ?District
    {
        return $this->district;
    }

    public function setDistrict(?District $district): static
    {
        $this->district = $district;

        return $this;
    }

    /**
     * @return Collection<int, HouseNumber>
     */
    public function getHouseNumbers(): Collection
    {
        return $this->houseNumbers;
    }

    public function addHouseNumber(HouseNumber $houseNumber): static
    {
        if (!$this->houseNumbers->contains($houseNumber)) {
            $this->houseNumbers->add($houseNumber);
            $houseNumber->setStreet($this);
        }

        return $this;
    }

    public function removeHouseNumber(HouseNumber $houseNumber): static
    {
        if ($this->houseNumbers->removeElement($houseNumber)) {
            // set the owning side to null (unless already changed)
            if ($houseNumber->getStreet() === $this) {
                $houseNumber->setStreet(null);
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

    public function addOfficialAddress(OfficialAddresses $officialAddress): static
    {
        if (!$this->officialAddresses->contains($officialAddress)) {
            $this->officialAddresses->add($officialAddress);
            $officialAddress->setStreet($this);
        }

        return $this;
    }

    public function removeOfficialAddress(OfficialAddresses $officialAddress): static
    {
        if ($this->officialAddresses->removeElement($officialAddress)) {
            // set the owning side to null (unless already changed)
            if ($officialAddress->getStreet() === $this) {
                $officialAddress->setStreet(null);
            }
        }

        return $this;
    }
}
