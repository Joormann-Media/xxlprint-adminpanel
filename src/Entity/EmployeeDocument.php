<?php

namespace App\Entity;

use App\Repository\EmployeeDocumentRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EmployeeDocumentRepository::class)]
class EmployeeDocument
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Employee::class, inversedBy: 'documents')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Employee $employee = null;

    #[ORM\Column(length: 100)]
    private ?string $docType = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $docDate = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $docExpires = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $docValidated = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $filename = null;
    
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmployee(): ?Employee
    {
        return $this->employee;
    }

    public function setEmployee(?Employee $employee): static
    {
        $this->employee = $employee;
        return $this;
    }

    public function getDocType(): ?string
    {
        return $this->docType;
    }

    public function setDocType(string $docType): static
    {
        $this->docType = $docType;
        return $this;
    }

    public function getDocDate(): ?\DateTimeInterface
    {
        return $this->docDate;
    }

    public function setDocDate(\DateTimeInterface $docDate): static
    {
        $this->docDate = $docDate;
        return $this;
    }

    public function getDocExpires(): ?\DateTimeInterface
    {
        return $this->docExpires;
    }

    public function setDocExpires(?\DateTimeInterface $docExpires): static
    {
        $this->docExpires = $docExpires;
        return $this;
    }

    public function getDocValidated(): ?\DateTimeInterface
    {
        return $this->docValidated;
    }

    public function setDocValidated(?\DateTimeInterface $docValidated): static
    {
        $this->docValidated = $docValidated;
        return $this;
    }
}
