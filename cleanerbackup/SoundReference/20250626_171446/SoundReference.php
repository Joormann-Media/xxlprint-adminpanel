<?php

namespace App\Entity;

use App\Repository\SoundReferenceRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SoundReferenceRepository::class)]
class SoundReference
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $roomName = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $soundFileID = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $soundFilepath = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $soundFilewave = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $newSoundFilepath = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $newSoundFilewave = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $soundFilemeta = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $newSoundFilemeta = null;

    #[ORM\ManyToOne(targetEntity: Project::class)]
    #[ORM\JoinColumn(name: 'project_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
    private ?Project $projectId = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRoomName(): ?string
    {
        return $this->roomName;
    }

    public function setRoomName(string $roomName): self
    {
        $this->roomName = $roomName;
        return $this;
    }

    public function getSoundFileID(): ?string
    {
        return $this->soundFileID;
    }

    public function setSoundFileID(string $soundFileID): self
    {
        $this->soundFileID = $soundFileID;
        return $this;
    }

    public function getSoundFilepath(): ?string
    {
        return $this->soundFilepath;
    }

    public function setSoundFilepath(string $soundFilepath): self
    {
        $this->soundFilepath = $soundFilepath;
        return $this;
    }

    public function getSoundFilewave(): ?string
    {
        return $this->soundFilewave;
    }

    public function setSoundFilewave(string $soundFilewave): self
    {
        $this->soundFilewave = $soundFilewave;
        return $this;
    }

    public function getNewSoundFilepath(): ?string
    {
        return $this->newSoundFilepath;
    }

    public function setNewSoundFilepath(?string $newSoundFilepath): self
    {
        $this->newSoundFilepath = $newSoundFilepath;
        return $this;
    }

    public function getNewSoundFilewave(): ?string
    {
        return $this->newSoundFilewave;
    }

    public function setNewSoundFilewave(?string $newSoundFilewave): self
    {
        $this->newSoundFilewave = $newSoundFilewave;
        return $this;
    }

    public function getSoundFilemeta(): ?string
    {
        return $this->soundFilemeta;
    }

    public function setSoundFilemeta(?string $soundFilemeta): self
    {
        $this->soundFilemeta = $soundFilemeta;
        return $this;
    }

    public function getNewSoundFilemeta(): ?string
    {
        return $this->newSoundFilemeta;
    }

    public function setNewSoundFilemeta(?string $newSoundFilemeta): self
    {
        $this->newSoundFilemeta = $newSoundFilemeta;
        return $this;
    }

    public function getProjectId(): ?Project
    {
        return $this->projectId;
    }

    public function setProjectId(?Project $projectId): self
    {
        $this->projectId = $projectId;
        return $this;
    }
}
