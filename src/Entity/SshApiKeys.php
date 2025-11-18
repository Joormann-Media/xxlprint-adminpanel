<?php

namespace App\Entity;

use App\Repository\SshApiKeysRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\User;

#[ORM\Entity(repositoryClass: SshApiKeysRepository::class)]
class SshApiKeys
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    private ?string $sshapikey = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: true)]
    private ?User $sshapikeyOwner = null;

    #[ORM\Column(nullable: true)]
    private ?string $sshapikeyDescription = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $sshapikeyExpiration = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $sshapikeyCreate = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $sshapikeyUpdate = null;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private ?bool $sshapikeyValid = null;

    #[ORM\PrePersist]
    public function onPrePersist(): void
    {
        $now = new \DateTimeImmutable();
        if (!$this->sshapikeyCreate) {
            $this->sshapikeyCreate = $now;
        }
        $this->sshapikeyUpdate = $now;
    }

    #[ORM\PreUpdate]
    public function onPreUpdate(): void
    {
        $this->sshapikeyUpdate = new \DateTimeImmutable();
    }

    // Getter & Setter

    public function getId(): ?int { return $this->id; }

    public function getSshapikey(): ?string { return $this->sshapikey; }
    public function setSshapikey(?string $sshapikey): self { $this->sshapikey = $sshapikey; return $this; }

    public function getSshapikeyOwner(): ?User { return $this->sshapikeyOwner; }
    public function setSshapikeyOwner(?User $sshapikeyOwner): self { $this->sshapikeyOwner = $sshapikeyOwner; return $this; }

    public function getSshapikeyDescription(): ?string { return $this->sshapikeyDescription; }
    public function setSshapikeyDescription(?string $sshapikeyDescription): self { $this->sshapikeyDescription = $sshapikeyDescription; return $this; }

    public function getSshapikeyExpiration(): ?\DateTimeInterface { return $this->sshapikeyExpiration; }
    public function setSshapikeyExpiration(?\DateTimeInterface $sshapikeyExpiration): self { $this->sshapikeyExpiration = $sshapikeyExpiration; return $this; }

    public function getSshapikeyCreate(): ?\DateTimeInterface { return $this->sshapikeyCreate; }

    public function getSshapikeyUpdate(): ?\DateTimeInterface { return $this->sshapikeyUpdate; }

    public function getSshapikeyValid(): ?bool { return $this->sshapikeyValid; }
    public function setSshapikeyValid(?bool $sshapikeyValid): self { $this->sshapikeyValid = $sshapikeyValid; return $this; }
}
