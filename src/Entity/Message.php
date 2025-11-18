<?php

namespace App\Entity;

use App\Entity\User;
use App\Entity\Attachment;
use App\Entity\MessageRecipient;
use App\Repository\MessageRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\Entity(repositoryClass: MessageRepository::class)]
#[ORM\Table(name: 'messages')]
class Message
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'text')]
    private string $content;

    #[ORM\ManyToOne(targetEntity: User::class)]
    private User $sender;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $createdAt;

    #[ORM\Column(type: 'string', length: 32)]
    private string $type; // z.B. 'chat', 'news', 'system'

    #[ORM\Column(type: 'boolean')]
    private bool $isUrgent = false;

    #[ORM\OneToMany(mappedBy: 'message', targetEntity: Attachment::class)]
    private Collection $attachments;

    #[ORM\OneToMany(mappedBy: 'message', targetEntity: MessageRecipient::class)]
    private Collection $recipients;

    // + ggf. ThreadId, Subject, etc.

    public function __construct()
    {
        $this->attachments = new ArrayCollection();
        $this->recipients = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;
        return $this;
    }

    public function getSender(): User
    {
        return $this->sender;
    }

    public function setSender(User $sender): self
    {
        $this->sender = $sender;
        return $this;
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;
        return $this;
    }

    public function isUrgent(): bool
    {
        return $this->isUrgent;
    }

    public function setIsUrgent(bool $isUrgent): self
    {
        $this->isUrgent = $isUrgent;
        return $this;
    }

    public function getAttachments(): Collection
    {
        return $this->attachments;
    }

    public function addAttachment(Attachment $attachment): self
    {
        if (!$this->attachments->contains($attachment)) {
            $this->attachments[] = $attachment;
            $attachment->setMessage($this);
        }
        return $this;
    }

    public function removeAttachment(Attachment $attachment): self
    {
        if ($this->attachments->removeElement($attachment)) {
            if ($attachment->getMessage() === $this) {
                $attachment->setMessage(null);
            }
        }
        return $this;
    }

    public function getRecipients(): Collection
    {
        return $this->recipients;
    }

    public function addRecipient(MessageRecipient $recipient): self
    {
        if (!$this->recipients->contains($recipient)) {
            $this->recipients[] = $recipient;
            $recipient->setMessage($this);
        }
        return $this;
    }

    public function removeRecipient(MessageRecipient $recipient): self
    {
        if ($this->recipients->removeElement($recipient)) {
            if ($recipient->getMessage() === $this) {
                $recipient->setMessage(null);
            }
        }
        return $this;
    }
    public function getRecipientGroups(): array
{
    $groups = [];
    foreach ($this->getRecipients() as $recipient) {
        if ($recipient->getRecipientGroup()) {
            $groups[] = $recipient->getRecipientGroup();
        }
    }
    return $groups;
}

}