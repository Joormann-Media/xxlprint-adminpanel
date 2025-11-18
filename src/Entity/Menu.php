<?php

namespace App\Entity;

use App\Repository\MenuRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;

#[ORM\Entity(repositoryClass: MenuRepository::class)]
class Menu
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $status = null;

    #[ORM\Column(name: "create_at", type: "datetime_immutable")]
    private ?\DateTimeImmutable $createAt = null;

    #[ORM\Column(name: "update_at", type: "datetime_immutable")]
    private ?\DateTimeImmutable $updateAt = null;

    #[ORM\Column(length: 255)]
    private ?string $updateBy = null;

    #[ORM\Column(length: 255)]
    private ?string $minRole = null;

    #[ORM\Column(type: Types::INTEGER)]
    private ?int $sortOrder = null;

    public function __construct()
    {
        $this->menuItems = new ArrayCollection();
        $this->childID = new ArrayCollection();
    }

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

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

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

    /**
     * @return Collection<int, MenuItem>
     */
    public function getMenuItems(): Collection
    {
        return $this->menuItems;
    }

    public function addMenuItem(MenuItem $menuItem): static
    {
        if (!$this->menuItems->contains($menuItem)) {
            $this->menuItems->add($menuItem);
            $menuItem->setMenu($this);
        }

        return $this;
    }

    public function removeMenuItem(MenuItem $menuItem): static
    {
        if ($this->menuItems->removeElement($menuItem)) {
            // set the owning side to null (unless already changed)
            if ($menuItem->getMenu() === $this) {
                $menuItem->setMenu(null);
            }
        }

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

    public function getMenuSubMenu(): ?MenuSubMenu
    {
        return $this->menuSubMenu;
    }

    public function setMenuSubMenu(?MenuSubMenu $menuSubMenu): static
    {
        $this->menuSubMenu = $menuSubMenu;

        return $this;
    }

    public function addChildID(MenuSubMenu $childID): static
    {
        if (!$this->childID->contains($childID)) {
            $this->childID->add($childID);
            $childID->setMenu($this);
        }

        return $this;
    }

    public function removeChildID(MenuSubMenu $childID): static
    {
        if ($this->childID->removeElement($childID)) {
            // set the owning side to null (unless already changed)
            if ($childID->getMenu() === $this) {
                $childID->setMenu(null);
            }
        }

        return $this;
    }

    public function getSortOrder(): ?int
    {
        return $this->sortOrder;
    }
    
    public function setSortOrder(int $sortOrder): static
    {
        $this->sortOrder = $sortOrder;
        return $this;
    }
    
}
