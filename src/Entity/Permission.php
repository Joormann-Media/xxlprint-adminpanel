<?php

namespace App\Entity;

use App\Repository\PermissionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PermissionRepository::class)]
class Permission
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $createdate = null;

    #[ORM\Column(length: 255)]
    private ?string $createBy = null;

    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'permissions')]
    private Collection $users;

    #[ORM\Column(length: 255)]
    private ?string $permissionRoute = null;

    #[ORM\Column(type: 'boolean', options: ['default' => true])]
    private bool $isActive = true;

    #[ORM\Column(type: 'boolean', nullable: true, options: ['default' => true])]
    private ?bool $onMobileIOS = true;

    #[ORM\Column(type: 'boolean', nullable: true, options: ['default' => true])]
    private ?bool $onMobileAndroid = true;

    #[ORM\Column(type: 'boolean', nullable: true, options: ['default' => true])]
    private ?bool $onOtherMobile = true;

    #[ORM\Column(type: 'boolean', nullable: true, options: ['default' => true])]
    private ?bool $onChromeOS = true;

    #[ORM\Column(type: 'boolean', nullable: true, options: ['default' => true])]
    private ?bool $onWindows = true;

    #[ORM\Column(type: 'boolean', nullable: true, options: ['default' => true])]
    private ?bool $onLinux = true;

    #[ORM\Column(type: 'boolean', nullable: true, options: ['default' => true])]
    private ?bool $onMacOS = true;

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $allowedCountries = null;

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $blockedCountries = null;

    #[ORM\Column(length: 255)]
    private ?string $minRole = null;

    #[ORM\Column]
    private ?bool $pinRequired = null;

    public function __construct()
    {
        $this->users = new ArrayCollection();
    }

    public function getId(): ?int { return $this->id; }
    public function getName(): ?string { return $this->name; }
    public function setName(string $name): static { $this->name = $name; return $this; }

    public function getDescription(): ?string { return $this->description; }
    public function setDescription(string $description): static { $this->description = $description; return $this; }

    public function getCreatedate(): ?\DateTimeInterface { return $this->createdate; }
    public function setCreatedate(\DateTimeInterface $createdate): static { $this->createdate = $createdate; return $this; }

    public function getCreateBy(): ?string { return $this->createBy; }
    public function setCreateBy(string $createBy): static { $this->createBy = $createBy; return $this; }

    public function getUsers(): Collection { return $this->users; }
    public function addUser(User $user): static
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
            $user->addPermission($this);
        }
        return $this;
    }

    public function removeUser(User $user): static
    {
        if ($this->users->removeElement($user)) {
            $user->removePermission($this);
        }
        return $this;
    }

    public function getPermissionRoute(): ?string { return $this->permissionRoute; }
    public function setPermissionRoute(string $permissionRoute): static { $this->permissionRoute = $permissionRoute; return $this; }

    public function isActive(): bool { return $this->isActive; }
    public function setIsActive(bool $isActive): static { $this->isActive = $isActive; return $this; }

    public function isOnMobileIOS(): bool { return $this->onMobileIOS; }
    public function setOnMobileIOS(bool $onMobileIOS): static { $this->onMobileIOS = $onMobileIOS; return $this; }

    public function isOnMobileAndroid(): bool { return $this->onMobileAndroid; }
    public function setOnMobileAndroid(bool $onMobileAndroid): static { $this->onMobileAndroid = $onMobileAndroid; return $this; }

    public function isOnOtherMobile(): bool { return $this->onOtherMobile; }
    public function setOnOtherMobile(bool $onOtherMobile): static { $this->onOtherMobile = $onOtherMobile; return $this; }

    public function isOnChromeOS(): bool { return $this->onChromeOS; }
    public function setOnChromeOS(bool $onChromeOS): static { $this->onChromeOS = $onChromeOS; return $this; }

    public function isOnWindows(): bool { return $this->onWindows; }
    public function setOnWindows(bool $onWindows): static { $this->onWindows = $onWindows; return $this; }

    public function isOnLinux(): bool { return $this->onLinux; }
    public function setOnLinux(bool $onLinux): static { $this->onLinux = $onLinux; return $this; }

    public function isOnMacOS(): bool { return $this->onMacOS; }
    public function setOnMacOS(bool $onMacOS): static { $this->onMacOS = $onMacOS; return $this; }

    public function getAllowedCountries(): ?array { return $this->allowedCountries; }
    public function setAllowedCountries(?array $allowedCountries): static { $this->allowedCountries = $allowedCountries; return $this; }

    public function getBlockedCountries(): ?array { return $this->blockedCountries; }
    public function setBlockedCountries(?array $blockedCountries): static { $this->blockedCountries = $blockedCountries; return $this; }

    public function getMinRole(): ?string
    {
        return $this->minRole;
    }

    public function setMinRole(string $minRole): static
    {
        $this->minRole = $minRole;

        return $this;
    }

    public function isPinRequired(): ?bool
    {
        return $this->pinRequired;
    }

    public function setPinRequired(bool $pinRequired): static
    {
        $this->pinRequired = $pinRequired;

        return $this;
    }
}
