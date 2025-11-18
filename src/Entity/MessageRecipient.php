<?php

namespace App\Entity;

use App\Repository\MessageRecipientRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity(repositoryClass: MessageRecipientRepository::class)]
class MessageRecipient
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Message::class, inversedBy: 'recipients')]
    private Message $message;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: true)]
    private ?User $recipientUser = null;

    #[ORM\ManyToOne(targetEntity: UserGroups::class)]
    #[ORM\JoinColumn(nullable: true)]
    private ?UserGroups $recipientGroup = null;

    #[ORM\Column(type: 'boolean')]
    private bool $isAll = false;

    #[ORM\OneToMany(mappedBy: 'recipient', targetEntity: MessageStatus::class)]
    private Collection $statuses;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $someIntegerField = null;

    public function __construct()
    {
        $this->statuses = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function isAll(): ?bool
    {
        return $this->isAll;
    }

    public function setIsAll(bool $isAll): static
    {
        $this->isAll = $isAll;

        return $this;
    }

    public function getSomeIntegerField(): ?int
    {
        return $this->someIntegerField;
    }

    public function setSomeIntegerField(?int $someIntegerField): static
    {
        $this->someIntegerField = $someIntegerField;

        return $this;
    }

    public function getMessage(): ?Message
    {
        return $this->message;
    }

    public function setMessage(?Message $message): static
    {
        $this->message = $message;

        return $this;
    }

    public function getRecipientUser(): ?User
    {
        return $this->recipientUser;
    }

    public function setRecipientUser(?User $recipientUser): static
    {
        $this->recipientUser = $recipientUser;

        return $this;
    }

    public function getRecipientGroup(): ?UserGroups
    {
        return $this->recipientGroup;
    }

    public function setRecipientGroup(?UserGroups $recipientGroup): static
    {
        $this->recipientGroup = $recipientGroup;

        return $this;
    }

    /**
     * @return Collection<int, MessageStatus>
     */
    public function getStatuses(): Collection
    {
        return $this->statuses;
    }

    public function addStatus(MessageStatus $status): static
    {
        if (!$this->statuses->contains($status)) {
            $this->statuses->add($status);
            $status->setRecipient($this);
        }

        return $this;
    }

    public function removeStatus(MessageStatus $status): static
    {
        if ($this->statuses->removeElement($status)) {
            // set the owning side to null (unless already changed)
            if ($status->getRecipient() === $this) {
                $status->setRecipient(null);
            }
        }

        return $this;
    }
}
