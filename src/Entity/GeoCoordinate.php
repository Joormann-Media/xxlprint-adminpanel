<?php

namespace App\Entity;

use App\Repository\GeoCoordinateRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GeoCoordinateRepository::class)]
#[ORM\Table(name: 'geo_coordinates')]
class GeoCoordinate
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'float')]
    private float $latitude;

    #[ORM\Column(type: 'float')]
    private float $longitude;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $altitude = null;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $accuracy = null;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    private ?string $source = null; // z. B. "nominatim", "gps", "manual"

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $capturedAt = null;

    #[ORM\Column(type: 'boolean', options: ['default' => true])]
    private bool $isActive = true;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function setLatitude(float $latitude): static
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function setLongitude(float $longitude): static
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function getAltitude(): ?float
    {
        return $this->altitude;
    }

    public function setAltitude(?float $altitude): static
    {
        $this->altitude = $altitude;

        return $this;
    }

    public function getAccuracy(): ?float
    {
        return $this->accuracy;
    }

    public function setAccuracy(?float $accuracy): static
    {
        $this->accuracy = $accuracy;

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

    public function getCapturedAt(): ?\DateTime
    {
        return $this->capturedAt;
    }

    public function setCapturedAt(?\DateTime $capturedAt): static
    {
        $this->capturedAt = $capturedAt;

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
}
