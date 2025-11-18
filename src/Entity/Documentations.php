<?php
// src/Entity/Documentations.php

namespace App\Entity;

use App\Entity\User;
use App\Repository\DocumentationsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DocumentationsRepository::class)]
class Documentations
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING, length: 255)]
    private ?string $docuName = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $docuCreate = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $docuUpdate = null;

    #[ORM\ManyToOne(inversedBy: 'documentations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $docuMaintainer = null;

    #[ORM\Column(type: Types::STRING, length: 50)]
    private ?string $docuVersion = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    private ?string $docuShortdescr = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $docuDescription = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDocuName(): ?string
    {
        return $this->docuName;
    }

    public function setDocuName(string $docuName): static
    {
        $this->docuName = $docuName;

        return $this;
    }

    public function getDocuCreate(): ?\DateTimeInterface
    {
        return $this->docuCreate;
    }

    public function setDocuCreate(\DateTimeInterface $docuCreate): static
    {
        $this->docuCreate = $docuCreate;

        return $this;
    }

    public function getDocuUpdate(): ?\DateTimeInterface
    {
        return $this->docuUpdate;
    }

    public function setDocuUpdate(?\DateTimeInterface $docuUpdate): static
    {
        $this->docuUpdate = $docuUpdate;

        return $this;
    }

    public function getDocuMaintainer(): ?User
    {
        return $this->docuMaintainer;
    }

    public function setDocuMaintainer(?User $docuMaintainer): static
    {
        $this->docuMaintainer = $docuMaintainer;

        return $this;
    }

    public function getDocuVersion(): ?string
    {
        return $this->docuVersion;
    }

    public function setDocuVersion(string $docuVersion): static
    {
        $this->docuVersion = $docuVersion;

        return $this;
    }

    public function getDocuShortdescr(): ?string
    {
        return $this->docuShortdescr;
    }

    public function setDocuShortdescr(?string $docuShortdescr): static
    {
        $this->docuShortdescr = $docuShortdescr;
        return $this;
    }

    public function getDocuDescription(): ?string
    {
        return $this->docuDescription;
    }

    public function setDocuDescription(?string $docuDescription): static
    {
        $this->docuDescription = $docuDescription;
        return $this;
    }
}
