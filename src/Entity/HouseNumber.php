<?php

namespace App\Entity;

use App\Repository\HouseNumberRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HouseNumberRepository::class)]
#[ORM\Table(name: 'house_numbers')]
class HouseNumber
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(length: 10)]
    private ?string $number = null;

    #[ORM\ManyToOne(targetEntity: Street::class, inversedBy: 'houseNumbers')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Street $street = null;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    private ?string $suffix = null; // z. B. „a“, „b“, „Hinterhaus“

    #[ORM\Column(type: 'boolean', options: ['default' => true])]
    private bool $isActive = true;

    #[ORM\OneToMany(mappedBy: 'houseNumber', targetEntity: OfficialAddresses::class)]
    private Collection $officialAddresses;

    public function __construct()
    {
        $this->officialAddresses = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumber(): ?string
    {
        return $this->number;
    }

    public function setNumber(string $number): static
    {
        $this->number = $number;

        return $this;
    }

    public function getSuffix(): ?string
    {
        return $this->suffix;
    }

    public function setSuffix(?string $suffix): static
    {
        $this->suffix = $suffix;

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

    public function getStreet(): ?Street
    {
        return $this->street;
    }

    public function setStreet(?Street $street): static
    {
        $this->street = $street;

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
            $officialAddress->setHouseNumber($this);
        }

        return $this;
    }

    public function removeOfficialAddress(OfficialAddresses $officialAddress): static
    {
        if ($this->officialAddresses->removeElement($officialAddress)) {
            // set the owning side to null (unless already changed)
            if ($officialAddress->getHouseNumber() === $this) {
                $officialAddress->setHouseNumber(null);
            }
        }

        return $this;
    }
}
