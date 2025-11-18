<?php

namespace App\Entity;

use App\Repository\ShortcodeButtonRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ShortcodeButtonRepository::class)]
class ShortcodeButton
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'string', length: 100, unique: true)]
    private string $tag = ''; // Default value to prevent uninitialized access

    #[ORM\Column(type: 'string', length: 255)]
    private string $route; // Symfony-Routenname

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $iconPath = null; // z. B. "gfx/shortcode/edit.png"

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $label = null; // z. B. "Bearbeiten"

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    private ?string $style = 'primary'; // Bootstrap-Stil: primary, danger, etc.

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $paramList = null; // z. B. "id,slug"

    #[ORM\Column(type: 'boolean')]
    private bool $isActive = true;

    #[ORM\Column(type: 'integer')]
    private int $sortOrder = 0;

    #[ORM\Column(type: 'string', length: 255)]
    private string $createdBy;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $updatedBy = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTime $updatedAt = null;

    public function __construct()
    {
        $this->tag = 'default_tag'; // Set a meaningful default value
        $this->createdBy = 'system'; // Default value for createdBy
        $this->createdAt = new \DateTimeImmutable(); // Initialize createdAt
    }

    // Getter + Setter generieren wir nach dem make:entity --regenerate

    public function getId(): int
    {
        return $this->id;
    }

    public function getTag(): string
    {
        return $this->tag;
    }

    public function setTag(string $tag): self
    {
        $this->tag = $tag;
        return $this;
    }

    public function getRoute(): string
    {
        return $this->route;
    }

    public function setRoute(string $route): self
    {
        $this->route = $route;
        return $this;
    }

    public function getIconPath(): ?string
    {
        return $this->iconPath;
    }

    public function setIconPath(?string $iconPath): self
    {
        $this->iconPath = $iconPath;
        return $this;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(?string $label): self
    {
        $this->label = $label;
        return $this;
    }

    public function getStyle(): ?string
    {
        return $this->style;
    }

    public function setStyle(?string $style): self
    {
        $this->style = $style;
        return $this;
    }

    public function getParamList(): ?string
    {
        return $this->paramList;
    }

    public function setParamList(?string $paramList): self
    {
        $this->paramList = $paramList;
        return $this;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;
        return $this;
    }

    public function getSortOrder(): int
    {
        return $this->sortOrder;
    }

    public function setSortOrder(int $sortOrder): self
    {
        $this->sortOrder = $sortOrder;
        return $this;
    }

    public function getCreatedBy(): string
    {
        return $this->createdBy;
    }

    public function setCreatedBy(string $createdBy): self
    {
        $this->createdBy = $createdBy;
        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getUpdatedBy(): ?string
    {
        return $this->updatedBy;
    }

    public function setUpdatedBy(?string $updatedBy): self
    {
        $this->updatedBy = $updatedBy;
        return $this;
    }

    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTime $updatedAt): self
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    public function __toString(): string
    {
        return $this->tag; // Use the 'tag' property as a string representation
    }
}
