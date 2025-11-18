<?php

namespace App\Entity;

use App\Entity\User;
use App\Entity\Message;
use App\Repository\ConversationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ConversationRepository::class)]
#[ORM\Table(name: 'conversations')]
class Conversation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'boolean')]
    private bool $isGroup = false;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $name = null;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $createdAt;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $lastMessageAt = null;

    #[ORM\ManyToMany(targetEntity: User::class)]
    #[ORM\JoinTable(name: 'conversation_participants')]
    private Collection $participants;



    public function __construct()
    {
        $this->participants = new ArrayCollection();
  
        $this->createdAt = new \DateTime();
    }

    // === Getter & Setter ===

    public function getId(): ?int {
        return $this->id;
    }

    public function isGroup(): bool {
        return $this->isGroup;
    }

    public function setIsGroup(bool $isGroup): self {
        $this->isGroup = $isGroup;
        return $this;
    }

    public function getName(): ?string {
        return $this->name;
    }

    public function setName(?string $name): self {
        $this->name = $name;
        return $this;
    }

    public function getCreatedAt(): \DateTimeInterface {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getLastMessageAt(): ?\DateTimeInterface {
        return $this->lastMessageAt;
    }

    public function setLastMessageAt(?\DateTimeInterface $lastMessageAt): self {
        $this->lastMessageAt = $lastMessageAt;
        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getParticipants(): Collection {
        return $this->participants;
    }

    public function addParticipant(User $user): self {
        if (!$this->participants->contains($user)) {
            $this->participants[] = $user;
        }
        return $this;
    }

    public function removeParticipant(User $user): self {
        $this->participants->removeElement($user);
        return $this;
    }

}
