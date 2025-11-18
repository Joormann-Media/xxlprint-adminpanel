<?php

namespace App\Entity;

use App\Repository\MenuItemRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MenuItemRepository::class)]
class MenuItem
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $route = null;

    #[ORM\Column(length: 255)]
    private ?string $minRole = null;

    #[ORM\Column(length: 255)]
    private ?string $status = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $lastUpdate = null;

    #[ORM\Column(length: 255)]
    private ?string $lastUpdateBy = null;

    #[ORM\Column(name: "create_at", type: "datetime_immutable")]
    private ?\DateTimeImmutable $createAt = null;

    #[ORM\Column(name: "update_at", type: "datetime_immutable")]
    private ?\DateTimeImmutable $updateAt = null;

    #[ORM\Column(length: 255)]
    private ?string $updateBy = null;

    #[ORM\Column(name: "menu_id", type: "integer", nullable: true)]
    private ?int $menuId = null;

    #[ORM\Column(name: "sub_menu_id", type: "integer", nullable: true)]
    private ?int $subMenuId = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $sortOrder = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getRoute(): ?string
    {
        return $this->route;
    }

    public function setRoute(string $route): static
    {
        $this->route = $route;

        return $this;
    }

    public function getMinRole(): ?string
    {
        return $this->minRole;
    }

    public function setMinRole(string $minRole): static
    {
        $this->minRole = $minRole;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getLastUpdate(): ?\DateTimeInterface
    {
        return $this->lastUpdate;
    }

    public function setLastUpdate(\DateTimeInterface $lastUpdate): static
    {
        $this->lastUpdate = $lastUpdate;

        return $this;
    }

    public function getLastUpdateBy(): ?string
    {
        return $this->lastUpdateBy;
    }

    public function setLastUpdateBy(string $lastUpdateBy): static
    {
        $this->lastUpdateBy = $lastUpdateBy;

        return $this;
    }

    public function getCreateAt(): ?\DateTimeImmutable
    {
        return $this->createAt;
    }

    public function setCreateAt(\DateTimeImmutable $createAt): static
    {
        $this->createAt = $createAt;

        return $this;
    }

    public function getUpdateAt(): ?\DateTimeImmutable
    {
        return $this->updateAt;
    }

    public function setUpdateAt(\DateTimeImmutable $updateAt): static
    {
        $this->updateAt = $updateAt;

        return $this;
    }

    public function getUpdateBy(): ?string
    {
        return $this->updateBy;
    }

    public function setUpdateBy(string $updateBy): static
    {
        $this->updateBy = $updateBy;

        return $this;
    }

    public function getMenuId(): ?int
    {
        return $this->menuId;
    }

    public function setMenuId(?int $menuId): static
    {
        $this->menuId = $menuId;

        return $this;
    }

    public function getSubMenuId(): ?int
    {
        return $this->subMenuId;
    }

    public function setSubMenuId(?int $subMenuId): static
    {
        $this->subMenuId = $subMenuId;

        return $this;
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
}
