<?php

namespace App\Entity;

use App\Repository\IconIndexRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: IconIndexRepository::class)]
class IconIndex
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $iconPath = null;

    #[ORM\Column(length: 255)]
    private ?string $iconName = null;

    #[ORM\Column(length: 255)]
    private ?string $iconCategory = null;

    #[ORM\Column(type: 'json')]
    private array $iconTags = [];

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIconPath(): ?string
    {
        return $this->iconPath;
    }

    public function setIconPath(string $iconPath): static
    {
        $this->iconPath = $iconPath;

        return $this;
    }

    public function getIconName(): ?string
    {
        return $this->iconName;
    }

    public function setIconName(string $iconName): static
    {
        $this->iconName = $iconName;

        return $this;
    }

    public function getIconCategory(): ?string
    {
        return $this->iconCategory;
    }

    public function setIconCategory(string $iconCategory): static
    {
        $this->iconCategory = $iconCategory;

        return $this;
    }

    public function getIconTags(): array
    {
        return $this->iconTags;
    }

    public function setIconTags(array $iconTags): static
    {
        $this->iconTags = $iconTags;

        return $this;
    }
}
