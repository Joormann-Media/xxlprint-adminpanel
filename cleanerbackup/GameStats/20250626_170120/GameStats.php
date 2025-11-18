<?php

namespace App\Entity;

use App\Repository\GameStatsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GameStatsRepository::class)]
class GameStats
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private ?int $id = null;

    #[ORM\Column(type: "datetime", nullable: true)]
    private ?\DateTimeInterface $statTimestamp = null;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private ?string $projectName = null;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private ?string $folder = null;

    #[ORM\Column(type: "integer", nullable: true)]
    private ?int $files = null;

    #[ORM\Column(type: "integer", nullable: true)]
    private ?int $images = null;

    #[ORM\Column(type: "integer", nullable: true)]
    private ?int $archives = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStatTimestamp(): ?\DateTimeInterface
    {
        return $this->statTimestamp;
    }

    public function setStatTimestamp(?\DateTimeInterface $statTimestamp): static
    {
        $this->statTimestamp = $statTimestamp;
        return $this;
    }

    public function getProjectName(): ?string
    {
        return $this->projectName;
    }

    public function setProjectName(?string $projectName): static
    {
        $this->projectName = $projectName;
        return $this;
    }

    public function getFolder(): ?string
    {
        return $this->folder;
    }

    public function setFolder(?string $folder): static
    {
        $this->folder = $folder;
        return $this;
    }

    public function getFiles(): ?int
    {
        return $this->files;
    }

    public function setFiles(?int $files): static
    {
        $this->files = $files;
        return $this;
    }

    public function getImages(): ?int
    {
        return $this->images;
    }

    public function setImages(?int $images): static
    {
        $this->images = $images;
        return $this;
    }

    public function getArchives(): ?int
    {
        return $this->archives;
    }

    public function setArchives(?int $archives): static
    {
        $this->archives = $archives;
        return $this;
    }
}
