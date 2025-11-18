<?php

namespace App\Entity;

use App\Repository\DevelopersBlogRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: DevelopersBlogRepository::class)]
#[ORM\Table(name: 'developers_blog')]
class DevelopersBlog
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank()]
    private string $title;

    #[ORM\Column(type: 'string', length: 255, unique: true)]
    #[Assert\NotBlank()]
    private string $slug;

    #[ORM\Column(type: 'text')]
    private string $content;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $excerpt = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $featuredImageUrl = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'author_id', referencedColumnName: 'id', nullable: false)]
    private ?User $author = null;

    #[ORM\Column(type: 'string', length: 20)]
    private string $status;

    #[ORM\Column(type: 'integer')]
    private int $readingTime;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $publishedAt = null;

    #[ORM\Column(type: 'datetime', options: ['default' => 'CURRENT_TIMESTAMP'])]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(type: 'datetime', options: ['default' => 'CURRENT_TIMESTAMP'])]
    private ?\DateTimeInterface $updatedAt = null;

    #[ORM\ManyToOne(targetEntity: Project::class)]
    #[ORM\JoinColumn(name: 'projekt_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
    private ?Project $projekt = null;
    
    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $tags = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $blogPostId = null;

    #[ORM\Column(type: 'boolean', options: ['default' => true])]
    private bool $commentsAllowed = true;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    private ?string $commentsVisibility = null;

    #[ORM\ManyToMany(targetEntity: User::class)]
    #[ORM\JoinTable(name: 'developers_blog_blocked_users')]
    private ?iterable $blockedUsers = null;

    #[ORM\ManyToOne(targetEntity: BlogCategory::class)]
    #[ORM\JoinColumn(nullable: true)]
    private ?BlogCategory $category = null;

    // Getter & Setter folgen hier

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

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = $slug;

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

    public function getExcerpt(): ?string
    {
        return $this->excerpt;
    }

    public function setExcerpt(?string $excerpt): static
    {
        $this->excerpt = $excerpt;

        return $this;
    }

    public function getFeaturedImageUrl(): ?string
    {
        return $this->featuredImageUrl;
    }

    public function setFeaturedImageUrl(?string $featuredImageUrl): static
    {
        $this->featuredImageUrl = $featuredImageUrl;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getReadingTime(): ?int
    {
        return $this->readingTime;
    }

    public function setReadingTime(int $readingTime): static
    {
        $this->readingTime = $readingTime;

        return $this;
    }

    public function getPublishedAt(): ?\DateTime
    {
        return $this->publishedAt;
    }

    public function setPublishedAt(?\DateTime $publishedAt): static
    {
        $this->publishedAt = $publishedAt;

        return $this;
    }

    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTime $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTime $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getTags(): ?string
    {
        return $this->tags;
    }

    public function setTags(?string $tags): static
    {
        $this->tags = $tags;

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

    public function getProjekt(): ?Project
    {
        return $this->projekt;
    }

    public function setProjekt(?Project $projekt): static
    {
        $this->projekt = $projekt;

        return $this;
    }

    public function getBlogPostId(): ?int
    {
        return $this->blogPostId;
    }

    public function setBlogPostId(?int $blogPostId): static
    {
        $this->blogPostId = $blogPostId;

        return $this;
    }

    public function isCommentsAllowed(): bool
    {
        return $this->commentsAllowed;
    }

    public function setCommentsAllowed(bool $commentsAllowed): static
    {
        $this->commentsAllowed = $commentsAllowed;

        return $this;
    }

    public function getCommentsVisibility(): ?string
    {
        return $this->commentsVisibility;
    }

    public function setCommentsVisibility(?string $commentsVisibility): static
    {
        $this->commentsVisibility = $commentsVisibility;

        return $this;
    }

    public function getBlockedUsers(): ?iterable
    {
        return $this->blockedUsers;
    }

    public function setBlockedUsers(?iterable $blockedUsers): static
    {
        $this->blockedUsers = $blockedUsers;

        return $this;
    }

    public function addBlockedUser(User $user): static
    {
        if ($this->blockedUsers === null) {
            $this->blockedUsers = [];
        }

        if (!in_array($user, $this->blockedUsers, true)) {
            $this->blockedUsers[] = $user;
        }

        return $this;
    }

    public function removeBlockedUser(User $user): static
    {
        if ($this->blockedUsers !== null) {
            $this->blockedUsers = array_filter(
                $this->blockedUsers,
                fn($blockedUser) => $blockedUser !== $user
            );
        }

        return $this;
    }
    public function getCategory(): ?BlogCategory
{
    return $this->category;
}

public function setCategory(?BlogCategory $category): self
{
    $this->category = $category;
    return $this;
}
}
