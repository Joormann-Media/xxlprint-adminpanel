<?php

namespace App\Entity;

use App\Repository\OfficialAddressesRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\PostalCode;
use App\Entity\City;
use App\Entity\District;
use App\Entity\Street;
use App\Entity\HouseNumber;
use App\Entity\GeoCoordinate;

#[ORM\Entity(repositoryClass: OfficialAddressesRepository::class)]
#[ORM\Table(name: 'official_addresses')]
class OfficialAddresses
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Street::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Street $street = null;

    #[ORM\ManyToOne(targetEntity: HouseNumber::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?HouseNumber $houseNumber = null;

    #[ORM\ManyToOne(targetEntity: PostalCode::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?PostalCode $postalCode = null;

    #[ORM\ManyToOne(targetEntity: City::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?City $city = null;

    #[ORM\ManyToOne(targetEntity: District::class)]
    #[ORM\JoinColumn(nullable: true)]
    private ?District $district = null;

    #[ORM\ManyToOne(targetEntity: GeoCoordinate::class)]
    #[ORM\JoinColumn(nullable: true)]
    private ?GeoCoordinate $coordinates = null;

    #[ORM\Column(type: 'boolean')]
    private bool $isValid = true;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $confidenceScore = null;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private ?string $source = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $correctedAt = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $notes = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getConfidenceScore(): ?float
    {
        return $this->confidenceScore;
    }

    public function setConfidenceScore(?float $confidenceScore): static
    {
        $this->confidenceScore = $confidenceScore;

        return $this;
    }

    public function getSource(): ?string
    {
        return $this->source;
    }

    public function setSource(?string $source): static
    {
        $this->source = $source;

        return $this;
    }

    public function getCorrectedAt(): ?\DateTime
    {
        return $this->correctedAt;
    }

    public function setCorrectedAt(?\DateTime $correctedAt): static
    {
        $this->correctedAt = $correctedAt;

        return $this;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function setNotes(?string $notes): static
    {
        $this->notes = $notes;

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

    public function getHouseNumber(): ?HouseNumber
    {
        return $this->houseNumber;
    }

    public function setHouseNumber(?HouseNumber $houseNumber): static
    {
        $this->houseNumber = $houseNumber;

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

    public function getCoordinates(): ?GeoCoordinate
    {
        return $this->coordinates;
    }

    public function setCoordinates(?GeoCoordinate $coordinates): static
    {
        $this->coordinates = $coordinates;

        return $this;
    }
}
