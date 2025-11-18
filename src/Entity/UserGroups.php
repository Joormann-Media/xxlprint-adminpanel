<?php

namespace App\Entity;

use App\Repository\UserGroupsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserGroupsRepository::class)]
class UserGroups
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $GroupName = null;

    #[ORM\Column(type: "text", nullable: true)]
private ?string $GroupDescription = null;


    #[ORM\Column(length: 255, nullable: true)]
    private ?string $groupLogo = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $groupCreate = null;

    #[ORM\Column(length: 255)]
    private ?string $groupCReateBy = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $groupMembers = null;

    #[ORM\Column(length: 255)]
    private ?string $baseDir = null;

    

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getGroupName(): ?string
    {
        return $this->GroupName;
    }

    public function setGroupName(string $GroupName): static
    {
        $this->GroupName = $GroupName;

        return $this;
    }

    public function getGroupDescription(): ?string
    {
        return $this->GroupDescription;
    }

    public function setGroupDescription(string $GroupDescription): static
    {
        $this->GroupDescription = $GroupDescription;

        return $this;
    }

    public function getGroupLogo(): ?string
    {
        return $this->groupLogo;
    }

    public function setGroupLogo(?string $groupLogo): static
    {
        $this->groupLogo = $groupLogo;

        return $this;
    }

    public function getGroupCreate(): ?\DateTimeInterface
    {
        return $this->groupCreate;
    }

    public function setGroupCreate(\DateTimeInterface $groupCreate): static
    {
        $this->groupCreate = $groupCreate;

        return $this;
    }

    public function getGroupCReateBy(): ?string
    {
        return $this->groupCReateBy;
    }

    public function setGroupCReateBy(string $groupCReateBy): static
    {
        $this->groupCReateBy = $groupCReateBy;

        return $this;
    }

    public function getGroupMembers(): ?string
    {
        return $this->groupMembers;
    }
    
    public function setGroupMembers(?string $groupMembers): self
    {
        $this->groupMembers = $groupMembers;
        return $this;
    }

    public function getBaseDir(): ?string
    {
        return $this->baseDir;
    }

    public function setBaseDir(string $baseDir): static
    {
        $this->baseDir = $baseDir;

        return $this;
    }
}
