<?php

namespace App\Entity;

use App\Repository\AdminConfigModulesRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AdminConfigModulesRepository::class)]
class AdminConfigModules
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $moduleName = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $moduleDescription = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $minRole = null;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $moduleCreate = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $moduleBy = null;

    public function __construct()
    {
        $this->moduleCreate = new \DateTimeImmutable(); // Beim Erstellen automatisch setzen
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getModuleName(): ?string
    {
        return $this->moduleName;
    }

    public function setModuleName(?string $moduleName): static
    {
        $this->moduleName = $moduleName;
        return $this;
    }

    public function getModuleDescription(): ?string
    {
        return $this->moduleDescription;
    }

    public function setModuleDescription(?string $moduleDescription): static
    {
        $this->moduleDescription = $moduleDescription;
        return $this;
    }

    public function getMinRole(): ?string
    {
        return $this->minRole;
    }

    public function setMinRole(?string $minRole): static
    {
        $this->minRole = $minRole;
        return $this;
    }

    public function getModuleCreate(): ?\DateTimeImmutable
    {
        return $this->moduleCreate;
    }

    public function setModuleCreate(?\DateTimeImmutable $moduleCreate): static
    {
        $this->moduleCreate = $moduleCreate;
        return $this;
    }

    public function getModuleBy(): ?string
    {
        return $this->moduleBy;
    }

    public function setModuleBy(?string $moduleBy): static
    {
        $this->moduleBy = $moduleBy;
        return $this;
    }
}
