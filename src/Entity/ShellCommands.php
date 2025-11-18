<?php

namespace App\Entity;

use App\Repository\ShellCommandsRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ShellCommandsRepository::class)]
class ShellCommands
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(name: 'command_short', type: 'string', length: 255, nullable: true)]
    private ?string $commandShort = null;

    #[ORM\Column(name: 'command_full', type: 'text', nullable: true)]
    private ?string $commandFull = null;

    #[ORM\Column(name: 'command_description', type: 'text', nullable: true)]
    private ?string $commandDescription = null;

    #[ORM\Column(name: 'command_category', type: 'string', length: 255, nullable: true)]
    private ?string $commandCategory = null;

    #[ORM\Column(name: 'command_createdate', type: 'datetime_immutable')]
    private ?\DateTimeImmutable $commandCreateDate = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private ?User $commandUser = null;

    public function __construct()
    {
        $this->commandCreateDate = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCommandShort(): ?string
    {
        return $this->commandShort;
    }

    public function setCommandShort(?string $commandShort): static
    {
        $this->commandShort = $commandShort;
        return $this;
    }

    public function getCommandFull(): ?string
    {
        return $this->commandFull;
    }

    public function setCommandFull(?string $commandFull): static
    {
        $this->commandFull = $commandFull;
        return $this;
    }

    public function getCommandDescription(): ?string
    {
        return $this->commandDescription;
    }

    public function setCommandDescription(?string $commandDescription): static
    {
        $this->commandDescription = $commandDescription;
        return $this;
    }

    public function getCommandCategory(): ?string
    {
        return $this->commandCategory;
    }

    public function setCommandCategory(?string $commandCategory): static
    {
        $this->commandCategory = $commandCategory;
        return $this;
    }

    public function getCommandCreateDate(): ?\DateTimeImmutable
    {
        return $this->commandCreateDate;
    }

    public function setCommandCreateDate(\DateTimeImmutable $commandCreateDate): static
    {
        $this->commandCreateDate = $commandCreateDate;
        return $this;
    }

    public function getCommandUser(): ?User
    {
        return $this->commandUser;
    }

    public function setCommandUser(?User $commandUser): static
    {
        $this->commandUser = $commandUser;
        return $this;
    }
}
