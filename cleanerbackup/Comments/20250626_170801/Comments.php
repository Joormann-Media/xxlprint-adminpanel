<?php

namespace App\Entity;

use App\Repository\CommentsRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CommentsRepository::class)]
#[ORM\Table(name: 'comments')]
class Comments
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'comment_user_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    #[Assert\NotNull]
    private ?User $commentUser = null;

    #[ORM\Column(type: 'text')]
    #[Assert\NotBlank]
    private string $commentText;

    #[ORM\Column(type: 'string', length: 20)]
    #[Assert\Choice(['public', 'private', 'internal', 'hidden'])]
    private string $commentVisibility = 'public';

    #[ORM\Column(type: 'datetime')]
    #[Assert\NotNull]
    private \DateTimeInterface $commentDate;

    #[ORM\Column(type: 'integer')]
    #[Assert\NotNull]
    private int $postId;

    public function __construct()
    {
        $this->commentDate = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCommentUser(): ?User
    {
        return $this->commentUser;
    }

    public function setCommentUser(User $commentUser): static
    {
        $this->commentUser = $commentUser;

        return $this;
    }

    public function getCommentText(): string
    {
        return $this->commentText;
    }

    public function setCommentText(string $commentText): static
    {
        $this->commentText = $commentText;

        return $this;
    }

    public function getCommentVisibility(): string
    {
        return $this->commentVisibility;
    }

    public function setCommentVisibility(string $commentVisibility): static
    {
        $this->commentVisibility = $commentVisibility;

        return $this;
    }

    public function getCommentDate(): \DateTimeInterface
    {
        return $this->commentDate;
    }

    public function setCommentDate(\DateTimeInterface $commentDate): static
    {
        $this->commentDate = $commentDate;

        return $this;
    }

    public function getPostId(): int
    {
        return $this->postId;
    }

    public function setPostId(int $postId): static
    {
        $this->postId = $postId;

        return $this;
    }
}
