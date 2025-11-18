<?php

namespace App\Entity;

use App\Repository\VoiceReferenceRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: VoiceReferenceRepository::class)]
class VoiceReference
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?string $roomName = null;

    #[ORM\Column(nullable: true)]
    private ?string $voiceFileID = null;

    #[ORM\Column(nullable: true)]
    private ?string $voiceFilepath = null;

    #[ORM\Column(nullable: true)]
    private ?string $voiceFilewave = null;

    #[ORM\Column(nullable: true)]
    private ?string $voiceFilemeta = null;

    #[ORM\Column(nullable: true)]
    private ?string $newVoiceFilepath = null;

    #[ORM\Column(nullable: true)]
    private ?string $newVoiceFilewave = null;

    #[ORM\Column(nullable: true)]
    private ?string $newVoiceFilemeta = null;

    #[ORM\ManyToOne(targetEntity: Project::class)]
    private ?Project $projectId = null;

    // Getter & Setter

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

    public function getVoiceFileID(): ?string
    {
        return $this->voiceFileID;
    }

    public function setVoiceFileID(?string $voiceFileID): static
    {
        $this->voiceFileID = $voiceFileID;
        return $this;
    }

    public function getVoiceFilepath(): ?string
    {
        return $this->voiceFilepath;
    }

    public function setVoiceFilepath(?string $voiceFilepath): static
    {
        $this->voiceFilepath = $voiceFilepath;
        return $this;
    }

    public function getVoiceFilewave(): ?string
    {
        return $this->voiceFilewave;
    }

    public function setVoiceFilewave(?string $voiceFilewave): static
    {
        $this->voiceFilewave = $voiceFilewave;
        return $this;
    }

    public function getVoiceFilemeta(): ?string
    {
        return $this->voiceFilemeta;
    }

    public function setVoiceFilemeta(?string $voiceFilemeta): static
    {
        $this->voiceFilemeta = $voiceFilemeta;
        return $this;
    }

    public function getNewVoiceFilepath(): ?string
    {
        return $this->newVoiceFilepath;
    }

    public function setNewVoiceFilepath(?string $newVoiceFilepath): static
    {
        $this->newVoiceFilepath = $newVoiceFilepath;
        return $this;
    }

    public function getNewVoiceFilewave(): ?string
    {
        return $this->newVoiceFilewave;
    }

    public function setNewVoiceFilewave(?string $newVoiceFilewave): static
    {
        $this->newVoiceFilewave = $newVoiceFilewave;
        return $this;
    }

    public function getNewVoiceFilemeta(): ?string
    {
        return $this->newVoiceFilemeta;
    }

    public function setNewVoiceFilemeta(?string $newVoiceFilemeta): static
    {
        $this->newVoiceFilemeta = $newVoiceFilemeta;
        return $this;
    }

    public function getProjectId(): ?Project
    {
        return $this->projectId;
    }

    public function setProjectId(?Project $projectId): static
    {
        $this->projectId = $projectId;
        return $this;
    }
}
