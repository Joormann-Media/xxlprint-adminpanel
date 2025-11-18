<?php

namespace App\Entity;

use App\Repository\GraphicsReferenceRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GraphicsReferenceRepository::class)]
class GraphicsReference
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?string $roomName = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $roomDate = null;

    #[ORM\Column(nullable: true)]
    private ?string $originalGraphicsPath = null;

    #[ORM\Column(nullable: true)]
    private ?string $originalGraphicsMeta = null;

    #[ORM\Column(nullable: true)]
    private ?string $reworkGraphicsPatch = null;

    #[ORM\Column(nullable: true)]
    private ?string $reworkGraphicsMeta = null;

    #[ORM\Column(nullable: true)]
    private ?string $reworkArtis = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRoomName(): ?string
    {
        return $this->roomName;
    }

    public function setRoomName(?string $roomName): static
    {
        $this->roomName = $roomName;
        return $this;
    }

    public function getRoomDate(): ?\DateTimeInterface
    {
        return $this->roomDate;
    }

    public function setRoomDate(?\DateTimeInterface $roomDate): static
    {
        $this->roomDate = $roomDate ?? new \DateTime();
        return $this;
    }

    public function getOriginalGraphicsPath(): ?string
    {
        return $this->originalGraphicsPath;
    }

    public function setOriginalGraphicsPath(?string $originalGraphicsPath): static
    {
        $this->originalGraphicsPath = $originalGraphicsPath;
        return $this;
    }

    public function getOriginalGraphicsMeta(): ?string
    {
        return $this->originalGraphicsMeta;
    }

    public function setOriginalGraphicsMeta(?string $originalGraphicsMeta): static
    {
        $this->originalGraphicsMeta = $originalGraphicsMeta;
        return $this;
    }

    public function getReworkGraphicsPatch(): ?string
    {
        return $this->reworkGraphicsPatch;
    }

    public function setReworkGraphicsPatch(?string $reworkGraphicsPatch): static
    {
        $this->reworkGraphicsPatch = $reworkGraphicsPatch;
        return $this;
    }

    public function getReworkGraphicsMeta(): ?string
    {
        return $this->reworkGraphicsMeta;
    }

    public function setReworkGraphicsMeta(?string $reworkGraphicsMeta): static
    {
        $this->reworkGraphicsMeta = $reworkGraphicsMeta;
        return $this;
    }

    public function getReworkArtis(): ?string
    {
        return $this->reworkArtis;
    }

    public function setReworkArtis(?string $reworkArtis): static
    {
        $this->reworkArtis = $reworkArtis;
        return $this;
    }
}
