<?php

namespace App\Entity;

use App\Repository\KidsBlacklistRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: KidsBlacklistRepository::class)]
class KidsBlacklist
{
        #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Schoolkids::class, inversedBy: 'blacklists')]
    #[ORM\JoinColumn(name: 'kid_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private ?Schoolkids $kid = null;

    #[ORM\ManyToOne(targetEntity: Schoolkids::class, inversedBy: 'blacklistedBy')]
    #[ORM\JoinColumn(name: 'enemy_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private ?Schoolkids $enemy = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $reason = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $createdAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    // --- Getter/Setter ---
    public function getKid(): ?Schoolkids { return $this->kid; }
    public function setKid(?Schoolkids $kid): static { $this->kid = $kid; return $this; }

    public function getEnemy(): ?Schoolkids { return $this->enemy; }
    public function setEnemy(?Schoolkids $enemy): static { $this->enemy = $enemy; return $this; }

    public function getReason(): ?string { return $this->reason; }
    public function setReason(?string $reason): static { $this->reason = $reason; return $this; }

    public function getCreatedAt(): ?\DateTimeInterface { return $this->createdAt; }
    public function setCreatedAt(?\DateTimeInterface $createdAt): static { $this->createdAt = $createdAt; return $this; }
}