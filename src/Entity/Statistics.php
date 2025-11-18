<?php

namespace App\Entity;

use App\Repository\StatisticsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StatisticsRepository::class)]
class Statistics
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $statsDate;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $panelStartet = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $totalFiles = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $totalFolders = null;

    public function __construct()
    {
        $this->statsDate = new \DateTimeImmutable(); // Automatischer Zeitstempel
    }

    // Optional: Getter + Setter
    public function getId(): ?int { return $this->id; }

    public function getStatsDate(): \DateTimeInterface { return $this->statsDate; }

    public function getPanelStartet(): ?int { return $this->panelStartet; }
    public function setPanelStartet(?int $panelStartet): self {
        $this->panelStartet = $panelStartet;
        return $this;
    }

    public function getTotalFiles(): ?int { return $this->totalFiles; }
    public function setTotalFiles(?int $totalFiles): self {
        $this->totalFiles = $totalFiles;
        return $this;
    }

    public function getTotalFolders(): ?int { return $this->totalFolders; }
    public function setTotalFolders(?int $totalFolders): self {
        $this->totalFolders = $totalFolders;
        return $this;
    }

    public function setStatsDate(\DateTime $statsDate): static
    {
        $this->statsDate = $statsDate;

        return $this;
    }
}
