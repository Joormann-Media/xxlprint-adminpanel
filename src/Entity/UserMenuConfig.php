<?php
// src/Entity/UserMenuConfig.php
namespace App\Entity;

use App\Repository\UserMenuConfigRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserMenuConfigRepository::class)]
class UserMenuConfig
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?int $sortOrder = null;

    #[ORM\ManyToOne(targetEntity: Menu::class)]
    #[ORM\JoinColumn(nullable: true)]
    private ?Menu $menuId = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $menuPosition = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSortOrder(): ?int
    {
        return $this->sortOrder;
    }

    public function setSortOrder(?int $sortOrder): static
    {
        $this->sortOrder = $sortOrder;

        return $this;
    }

    public function getMenuId(): ?Menu
    {
        return $this->menuId;
    }

    public function setMenuId(?Menu $menuId): static
    {
        $this->menuId = $menuId;

        return $this;
    }

    public function getMenuPosition(): ?string
    {
        return $this->menuPosition;
    }

    public function setMenuPosition(?string $menuPosition): static
    {
        $this->menuPosition = $menuPosition;

        return $this;
    }
}
