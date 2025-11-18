<?php

namespace App\Entity;

use App\Repository\UserOnlineStatusRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserOnlineStatusRepository::class)]
class UserOnlineStatus
{
#[ORM\Id]
#[ORM\GeneratedValue]
#[ORM\Column(type: 'integer')]  // ← Das hat gefehlt!
private ?int $id = null;


    #[ORM\OneToOne(inversedBy: 'onlineStatus', targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column(type: 'boolean')]
    private bool $isOnline = false;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $lastSeenAt;

    public function __construct()
    {
        $this->lastSeenAt = new \DateTime();
    }

    public function getId(): ?int
{
    return $this->id;
}

public function getUser(): ?User
{
    return $this->user;
}

public function setUser(User $user): self
{
    $this->user = $user;
    return $this;
}

public function isOnline(): bool
{
    return $this->isOnline;
}

public function setIsOnline(bool $isOnline): self
{
    $this->isOnline = $isOnline;
    return $this;
}

public function getLastSeenAt(): \DateTimeInterface
{
    return $this->lastSeenAt;
}

public function setLastSeenAt(\DateTimeInterface $lastSeenAt): self
{
    $this->lastSeenAt = $lastSeenAt;
    return $this;
}

}
