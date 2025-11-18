<?php

namespace App\Entity;

use App\Repository\NewsStatusRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: NewsStatusRepository::class)]
class NewsStatus
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    private User $user;

    #[ORM\ManyToOne]
    private News $news;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTime $shownAt = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTime $readAt = null;

    #[ORM\Column(type: 'boolean')]
    private bool $pinned = false;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getShownAt(): ?\DateTime
    {
        return $this->shownAt;
    }

    public function setShownAt(?\DateTime $shownAt): static
    {
        $this->shownAt = $shownAt;

        return $this;
    }

    public function getReadAt(): ?\DateTime
    {
        return $this->readAt;
    }

    public function setReadAt(?\DateTime $readAt): static
    {
        $this->readAt = $readAt;

        return $this;
    }

    public function isPinned(): ?bool
    {
        return $this->pinned;
    }

    public function setPinned(bool $pinned): static
    {
        $this->pinned = $pinned;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getNews(): ?News
    {
        return $this->news;
    }

    public function setNews(?News $news): static
    {
        $this->news = $news;

        return $this;
    }
}