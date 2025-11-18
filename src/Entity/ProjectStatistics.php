<?php

namespace App\Entity;

use App\Repository\ProjectStatisticsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProjectStatisticsRepository::class)]
class ProjectStatistics
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $createdAt;

    #[ORM\Column(type: 'integer')]
    private int $entitiesCount;

    #[ORM\Column(type: 'integer')]
    private int $routesCount;

    #[ORM\Column(type: 'integer')]
    private int $formsCount;

    #[ORM\Column(type: 'integer')]
    private int $controllersCount;

    #[ORM\Column(type: 'integer')]
    private int $filesCount;

    #[ORM\Column(type: 'integer')]
    private int $directoriesCount;

    #[ORM\Column(type: 'integer')]
    private int $servicesCount;

    #[ORM\Column(type: 'integer')]
    private int $commandsCount;

    #[ORM\Column(type: 'integer')]
    private int $shellScriptsCount;

    #[ORM\Column(type: 'integer')]
    private int $pythonScriptsCount;

    #[ORM\Column(type: 'integer')]
    private int $phpLinesCount;

    #[ORM\Column(type: 'integer')]
    private int $pythonLinesCount;

    #[ORM\Column(type: 'integer')]
    private int $shellLinesCount;

    #[ORM\Column(type: 'integer')]
    private int $totalLinesCount;

    // ========== GETTER & SETTER ==========

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getEntitiesCount(): int
    {
        return $this->entitiesCount;
    }

    public function setEntitiesCount(int $count): self
    {
        $this->entitiesCount = $count;
        return $this;
    }

    public function getRoutesCount(): int
    {
        return $this->routesCount;
    }

    public function setRoutesCount(int $count): self
    {
        $this->routesCount = $count;
        return $this;
    }

    public function getFormsCount(): int
    {
        return $this->formsCount;
    }

    public function setFormsCount(int $count): self
    {
        $this->formsCount = $count;
        return $this;
    }

    public function getControllersCount(): int
    {
        return $this->controllersCount;
    }

    public function setControllersCount(int $count): self
    {
        $this->controllersCount = $count;
        return $this;
    }

    public function getFilesCount(): int
    {
        return $this->filesCount;
    }

    public function setFilesCount(int $count): self
    {
        $this->filesCount = $count;
        return $this;
    }

    public function getDirectoriesCount(): int
    {
        return $this->directoriesCount;
    }

    public function setDirectoriesCount(int $count): self
    {
        $this->directoriesCount = $count;
        return $this;
    }

    public function getServicesCount(): int
    {
        return $this->servicesCount;
    }

    public function setServicesCount(int $count): self
    {
        $this->servicesCount = $count;
        return $this;
    }

    public function getCommandsCount(): int
    {
        return $this->commandsCount;
    }

    public function setCommandsCount(int $count): self
    {
        $this->commandsCount = $count;
        return $this;
    }

    public function getShellScriptsCount(): int
    {
        return $this->shellScriptsCount;
    }

    public function setShellScriptsCount(int $count): self
    {
        $this->shellScriptsCount = $count;
        return $this;
    }

    public function getPythonScriptsCount(): int
    {
        return $this->pythonScriptsCount;
    }

    public function setPythonScriptsCount(int $count): self
    {
        $this->pythonScriptsCount = $count;
        return $this;
    }

    public function getPhpLinesCount(): int
    {
        return $this->phpLinesCount;
    }

    public function setPhpLinesCount(int $count): self
    {
        $this->phpLinesCount = $count;
        return $this;
    }

    public function getPythonLinesCount(): int
    {
        return $this->pythonLinesCount;
    }

    public function setPythonLinesCount(int $count): self
    {
        $this->pythonLinesCount = $count;
        return $this;
    }

    public function getShellLinesCount(): int
    {
        return $this->shellLinesCount;
    }

    public function setShellLinesCount(int $count): self
    {
        $this->shellLinesCount = $count;
        return $this;
    }

    public function getTotalLinesCount(): int
    {
        return $this->totalLinesCount;
    }

    public function setTotalLinesCount(int $count): self
    {
        $this->totalLinesCount = $count;
        return $this;
    }
}
