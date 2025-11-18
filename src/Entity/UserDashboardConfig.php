<?php

namespace App\Entity;

use App\Repository\UserDashboardConfigRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserDashboardConfigRepository::class)]
class UserDashboardConfig
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?DashboardModules $module = null;

    #[ORM\Column(type: 'integer')]
    private ?int $sortOrder = 0;

    #[ORM\Column(type: 'boolean')]
    private bool $isVisible = true;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $updatedAt = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $position = null;

    #[ORM\Column(type: 'json', options: ['default' => '[]'])]
    private array $settings = [];

    #[\Symfony\Component\Serializer\Annotation\Ignore]
    public ?string $renderedContent = null;

    private string $name;
    private string $content;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;
        return $this;
    }

    public function getModule(): ?DashboardModules
    {
        return $this->module;
    }

    public function setModule(?DashboardModules $module): self
    {
        $this->module = $module;
        return $this;
    }

    public function getModules(): array
    {
        // Assuming this entity is meant to handle multiple modules in some way
        return $this->module ? [$this->module] : [];
    }

    public function getSortOrder(): ?int
    {
        return $this->sortOrder;
    }

    public function setSortOrder(?int $sortOrder): self
    {
        $this->sortOrder = $sortOrder;
        return $this;
    }

    public function getIsVisible(): bool
    {
        return $this->isVisible;
    }

    public function setIsVisible(bool $isVisible): self
    {
        $this->isVisible = $isVisible;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    public function getPosition(): ?string
    {
        return $this->position;
    }

    public function setPosition(?string $position): self
    {
        $this->position = $position;
        return $this;
    }

    public function getSettings(): array
    {
        return is_array($this->settings) ? $this->settings : [];
    }

    public function setSettings(array $settings): self
    {
        // Sicherheit: JSON-kompatibel?
        $this->settings = is_array($settings) ? $settings : [];
        return $this;
    }

    public function getName(): string
    {
        return $this->module ? $this->module->getName() : '';
    }

    public function getContent(): string
    {
        return $this->module ? $this->module->getContent() : '';
    }
}
