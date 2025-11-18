<?php

namespace App\Entity;

use App\Repository\UserRolesRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserRolesRepository::class)]
class UserRoles
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $roleName = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $roleCreate = null;

    #[ORM\Column(length: 255)]
    private ?string $roleCreateBy = null;

    #[ORM\Column(length: 255)]
    private ?string $roleDescription = null;

    #[ORM\Column(length: 255)]
    private ?string $roleTag = null;

    #[ORM\Column]
    private ?int $hierarchy = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRoleName(): ?string
    {
        return $this->roleName;
    }

    public function setRoleName(string $roleName): static
    {
        $this->roleName = $roleName;

        return $this;
    }

    public function getRoleCreate(): ?\DateTimeInterface
    {
        return $this->roleCreate;
    }

    public function setRoleCreate(\DateTimeInterface $roleCreate): static
    {
        $this->roleCreate = $roleCreate;

        return $this;
    }

    public function getRoleCreateBy(): ?string
    {
        return $this->roleCreateBy;
    }

    public function setRoleCreateBy(string $roleCreateBy): static
    {
        $this->roleCreateBy = $roleCreateBy;

        return $this;
    }

    public function getRoleDescription(): ?string
    {
        return $this->roleDescription;
    }

    public function setRoleDescription(string $roleDescription): static
    {
        $this->roleDescription = $roleDescription;

        return $this;
    }

    public function getRoleTag(): ?string
    {
        return $this->roleTag;
    }

    public function setRoleTag(string $roleTag): static
    {
        $this->roleTag = $roleTag;

        return $this;
    }

    public function getHierarchy(): ?int
    {
        return $this->hierarchy;
    }

    public function setHierarchy(int $hierarchy): static
    {
        $this->hierarchy = $hierarchy;

        return $this;
    }

}
