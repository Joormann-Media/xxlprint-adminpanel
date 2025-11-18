<?php

namespace App\Entity;

use App\Repository\DiscordWebhookHistoryRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: DiscordWebhookHistoryRepository::class)]
class DiscordWebhookHistory
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $timestamp;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $username = null; // Beziehung zur User-Entität

    #[ORM\Column(type: 'text')]
    private ?string $hooktext = null;

    #[ORM\Column(length: 50)]
    private ?string $hookstatus = null; // z.B. success, error, pending

    public function __construct()
    {
        $this->timestamp = new \DateTimeImmutable();
    }

    // Getter & Setter

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTimestamp(): \DateTimeImmutable
    {
        return $this->timestamp;
    }

    public function setTimestamp(\DateTimeImmutable $timestamp): self
    {
        $this->timestamp = $timestamp;
        return $this;
    }

    public function getUsername(): ?User
    {
        return $this->username;
    }

    public function setUsername(?User $user): self
    {
        $this->username = $user;
        return $this;
    }

    public function getHooktext(): ?string
    {
        return $this->hooktext;
    }

    public function setHooktext(string $hooktext): self
    {
        $this->hooktext = $hooktext;
        return $this;
    }

    public function getHookstatus(): ?string
    {
        return $this->hookstatus;
    }

    public function setHookstatus(string $hookstatus): self
    {
        $this->hookstatus = $hookstatus;
        return $this;
    }
}
