<?php

namespace App\Entity;

use App\Repository\MessageStatusRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MessageStatusRepository::class)]
class MessageStatus
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: MessageRecipient::class, inversedBy: 'statuses')]
    private MessageRecipient $recipient;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $deliveredAt = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $displayedAt = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $confirmedAt = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $deviceInfo = null;

    #[ORM\Column(type: 'string', length: 45, nullable: true)]
    private ?string $ip = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDeliveredAt(): ?\DateTimeInterface
{
    return $this->deliveredAt;
}
public function setDeliveredAt(?\DateTimeInterface $deliveredAt): static
{
    $this->deliveredAt = $deliveredAt;
    return $this;
}

    
// ... analog für displayedAt und confirmedAt

    public function getDisplayedAt(): ?\DateTime
    {
        return $this->displayedAt;
    }

    public function setDisplayedAt(?\DateTime $displayedAt): static
    {
        $this->displayedAt = $displayedAt;

        return $this;
    }

    public function getConfirmedAt(): ?\DateTime
    {
        return $this->confirmedAt;
    }

    public function setConfirmedAt(?\DateTime $confirmedAt): static
    {
        $this->confirmedAt = $confirmedAt;

        return $this;
    }

    public function getDeviceInfo(): ?string
    {
        return $this->deviceInfo;
    }

    public function setDeviceInfo(?string $deviceInfo): static
    {
        $this->deviceInfo = $deviceInfo;

        return $this;
    }

    public function getIp(): ?string
    {
        return $this->ip;
    }

    public function setIp(?string $ip): static
    {
        $this->ip = $ip;

        return $this;
    }

    public function getRecipient(): ?MessageRecipient
    {
        return $this->recipient;
    }

    public function setRecipient(?MessageRecipient $recipient): static
    {
        $this->recipient = $recipient;

        return $this;
    }

}