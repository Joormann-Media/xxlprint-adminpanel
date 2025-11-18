<?php

namespace App\Entity;

use App\Repository\GpsPositionRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GpsPositionRepository::class)]
class GpsPosition
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $timestampUtc;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 6)]
    private float $latitude;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 6)]
    private float $longitude;

    #[ORM\Column(type: 'decimal', precision: 6, scale: 2, nullable: true)]
    private ?float $speed = null;

    #[ORM\Column(type: 'decimal', precision: 6, scale: 2, nullable: true)]
    private ?float $course = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $statusText = null;

    #[ORM\Column(type: 'string', length: 7, nullable: true)]
    private ?string $color = null;

    #[ORM\Column(type: 'integer')]
    private int $clientId;

    #[ORM\Column(type: 'datetime_immutable', options: ['default' => 'CURRENT_TIMESTAMP'])]
    private \DateTimeImmutable $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    // → Getters & Setters (bei Bedarf generierbar)

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTimestampUtc(): ?\DateTimeImmutable
    {
        return $this->timestampUtc;
    }

    public function setTimestampUtc(\DateTimeImmutable $timestampUtc): static
    {
        $this->timestampUtc = $timestampUtc;

        return $this;
    }

    public function getLatitude(): ?string
    {
        return $this->latitude;
    }

    public function setLatitude(string $latitude): static
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLongitude(): ?string
    {
        return $this->longitude;
    }

    public function setLongitude(string $longitude): static
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function getSpeed(): ?string
    {
        return $this->speed;
    }

    public function setSpeed(?string $speed): static
    {
        $this->speed = $speed;

        return $this;
    }

    public function getCourse(): ?string
    {
        return $this->course;
    }

    public function setCourse(?string $course): static
    {
        $this->course = $course;

        return $this;
    }

    public function getStatusText(): ?string
    {
        return $this->statusText;
    }

    public function setStatusText(?string $statusText): static
    {
        $this->statusText = $statusText;

        return $this;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(?string $color): static
    {
        $this->color = $color;

        return $this;
    }

    public function getClientId(): ?int
    {
        return $this->clientId;
    }

    public function setClientId(int $clientId): static
    {
        $this->clientId = $clientId;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}
