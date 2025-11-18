<?php

namespace App\Entity;

use App\Repository\NewsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: NewsRepository::class)]
class News
{
     #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private string $title;

    #[ORM\Column(type: 'text')]
    private string $content;

    #[ORM\Column(type: 'datetime')]
    private \DateTime $publishAt;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTime $expiresAt = null;

    #[ORM\Column(type: 'boolean')]
    private bool $popupOnLogin = true;

    #[ORM\Column(type: 'boolean')]
    private bool $isSticky = false;

    #[ORM\ManyToOne]
    private ?User $author = null;

    // Targeting
    #[ORM\ManyToMany(targetEntity: User::class)]
    private Collection $targetUsers;

    #[ORM\ManyToMany(targetEntity: UserGroups::class)]
    private Collection $targetGroups;

    public function __construct()
    {
        $this->targetUsers = new ArrayCollection();
        $this->targetGroups = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getPublishAt(): ?\DateTime
    {
        return $this->publishAt;
    }

    public function setPublishAt(\DateTime $publishAt): static
    {
        $this->publishAt = $publishAt;

        return $this;
    }

    public function getExpiresAt(): ?\DateTime
    {
        return $this->expiresAt;
    }

    public function setExpiresAt(?\DateTime $expiresAt): static
    {
        $this->expiresAt = $expiresAt;

        return $this;
    }

    public function isPopupOnLogin(): ?bool
    {
        return $this->popupOnLogin;
    }

    public function setPopupOnLogin(bool $popupOnLogin): static
    {
        $this->popupOnLogin = $popupOnLogin;

        return $this;
    }

    public function isSticky(): ?bool
    {
        return $this->isSticky;
    }

    public function setIsSticky(bool $isSticky): static
    {
        $this->isSticky = $isSticky;

        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): static
    {
        $this->author = $author;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getTargetUsers(): Collection
    {
        return $this->targetUsers;
    }

    public function addTargetUser(User $targetUser): static
    {
        if (!$this->targetUsers->contains($targetUser)) {
            $this->targetUsers->add($targetUser);
        }

        return $this;
    }

    public function removeTargetUser(User $targetUser): static
    {
        $this->targetUsers->removeElement($targetUser);

        return $this;
    }

    /**
     * @return Collection<int, UserGroups>
     */
    public function getTargetGroups(): Collection
    {
        return $this->targetGroups;
    }

    public function addTargetGroup(UserGroups $targetGroup): static
    {
        if (!$this->targetGroups->contains($targetGroup)) {
            $this->targetGroups->add($targetGroup);
        }

        return $this;
    }

    public function removeTargetGroup(UserGroups $targetGroup): static
    {
        $this->targetGroups->removeElement($targetGroup);

        return $this;
    }
}
