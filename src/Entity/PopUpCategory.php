<?php

// src/Entity/PopUpCategory.php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\PopUpCategoryRepository;

#[ORM\Entity(repositoryClass: PopUpCategoryRepository::class)]
class PopUpCategory
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $categoryName;

    #[ORM\Column(type: 'datetime')]
    private $erstelltAm;

    #[ORM\Column(type: 'string', length: 255)]
    private $erstelltVon;

    #[ORM\Column(type: 'text', nullable: true)]
    private $description;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCategoryName(): ?string
    {
        return $this->categoryName;
    }

    public function setCategoryName(string $categoryName): self
    {
        $this->categoryName = $categoryName;

        return $this;
    }

    public function getErstelltAm(): ?\DateTimeInterface
    {
        return $this->erstelltAm;
    }

    public function setErstelltAm(\DateTimeInterface $erstelltAm): self
    {
        $this->erstelltAm = $erstelltAm;

        return $this;
    }

    public function getErstelltVon(): ?string
    {
        return $this->erstelltVon;
    }

    public function setErstelltVon(string $erstelltVon): self
    {
        $this->erstelltVon = $erstelltVon;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }
}

