<?php

namespace App\Entity;

use App\Repository\PenaltyCatalogEntryRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PenaltyCatalogEntryRepository::class)]
class PenaltyCatalogEntry
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    private string $offenseTitle;

    #[ORM\Column(type: 'text')]
    private string $description;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $paragraph = null;

    #[ORM\Column(length: 100)]
    private string $category;

    #[ORM\Column(type: 'json')]
    private array $vehicleTypes = [];

    #[ORM\Column(type: 'integer')]
    private int $penaltyMin;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $penaltyMax = null;

    #[ORM\Column(type: 'integer')]
    private int $points = 0;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $drivingBanMonths = null;

    #[ORM\Column(type: 'boolean')]
    private bool $isProbezeitRelevant = false;

    #[ORM\Column(length: 50)]
    private string $severityLevel = 'mittel';

    #[ORM\Column(type: 'boolean')]
    private bool $active = true;

    // Getter & Setter kannst du generieren lassen oder ich bau sie dir.

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOffenseTitle(): ?string
    {
        return $this->offenseTitle;
    }

    public function setOffenseTitle(string $offenseTitle): static
    {
        $this->offenseTitle = $offenseTitle;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getParagraph(): ?string
    {
        return $this->paragraph;
    }

    public function setParagraph(?string $paragraph): static
    {
        $this->paragraph = $paragraph;

        return $this;
    }

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function setCategory(string $category): static
    {
        $this->category = $category;

        return $this;
    }

    public function getVehicleTypes(): array
    {
        return $this->vehicleTypes;
    }

    public function setVehicleTypes(array $vehicleTypes): static
    {
        $this->vehicleTypes = $vehicleTypes;

        return $this;
    }

    public function getPenaltyMin(): ?int
    {
        return $this->penaltyMin;
    }

    public function setPenaltyMin(int $penaltyMin): static
    {
        $this->penaltyMin = $penaltyMin;

        return $this;
    }

    public function getPenaltyMax(): ?int
    {
        return $this->penaltyMax;
    }

    public function setPenaltyMax(?int $penaltyMax): static
    {
        $this->penaltyMax = $penaltyMax;

        return $this;
    }

    public function getPoints(): ?int
    {
        return $this->points;
    }

    public function setPoints(int $points): static
    {
        $this->points = $points;

        return $this;
    }

    public function getDrivingBanMonths(): ?int
    {
        return $this->drivingBanMonths;
    }

    public function setDrivingBanMonths(?int $drivingBanMonths): static
    {
        $this->drivingBanMonths = $drivingBanMonths;

        return $this;
    }

    public function isProbezeitRelevant(): ?bool
    {
        return $this->isProbezeitRelevant;
    }

    public function setIsProbezeitRelevant(bool $isProbezeitRelevant): static
    {
        $this->isProbezeitRelevant = $isProbezeitRelevant;

        return $this;
    }

    public function getSeverityLevel(): ?string
    {
        return $this->severityLevel;
    }

    public function setSeverityLevel(string $severityLevel): static
    {
        $this->severityLevel = $severityLevel;

        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): static
    {
        $this->active = $active;

        return $this;
    }
}
