<?php

namespace App\Entity;

use App\Repository\DialogueReferenceRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DialogueReferenceRepository::class)]
class DialogueReference
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $roomId = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $dialogId = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $dialogText = null;

    #[ORM\Column(length: 10, nullable: true)]
    private ?string $dialogLang = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRoomId(): ?string
    {
        return $this->roomId;
    }

    public function setRoomId(?string $roomId): static
    {
        $this->roomId = $roomId;
        return $this;
    }

    public function getDialogId(): ?string
    {
        return $this->dialogId;
    }

    public function setDialogId(?string $dialogId): static
    {
        $this->dialogId = $dialogId;
        return $this;
    }

    public function getDialogText(): ?string
    {
        return $this->dialogText;
    }

    public function setDialogText(?string $dialogText): static
    {
        $this->dialogText = $dialogText;
        return $this;
    }

    public function getDialogLang(): ?string
    {
        return $this->dialogLang;
    }

    public function setDialogLang(?string $dialogLang): static
    {
        $this->dialogLang = $dialogLang;
        return $this;
    }
}
