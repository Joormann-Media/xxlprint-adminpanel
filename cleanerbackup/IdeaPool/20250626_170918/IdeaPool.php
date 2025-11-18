<?php

namespace App\Entity;

use App\Repository\IdeaPoolRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;

#[ORM\Entity(repositoryClass: IdeaPoolRepository::class)]
class IdeaPool
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    // Wer hat's angelegt? (optional)
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: "SET NULL")]
    private ?User $user = null;

    #[ORM\Column(type: Types::STRING, length: 255)]
    private ?string $title = null;

    // Hauptinhalt (Text, JSON, Markdown, whatever)
    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $content = null;

    // "text", "audio", "image", "todo", "idea", "link", ...
    #[ORM\Column(type: Types::STRING, length: 32, options: ['default' => 'text'])]
    private ?string $type = 'text';

    // z.B. "offen", "in Bearbeitung", "erledigt", "archiviert"
    #[ORM\Column(type: Types::STRING, length: 32, options: ['default' => 'offen'])]
    private ?string $status = 'offen';

    // Tags als Komma-separierte Liste, wenn du willst (optional)
    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    private ?string $tags = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private ?\DateTimeImmutable $createdAt = null;

    // Optional: Datei-URL/Name, falls Bild/Audio
    #[ORM\Column(type: Types::STRING, length: 255, nullable: true)]
    private ?string $file = null;

    // Optional: Quelle (z.B. "app", "admin", "api", "import")
    #[ORM\Column(type: Types::STRING, length: 64, nullable: true)]
    private ?string $source = null;

    // Getter & Setter generiert dein Patcher!

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

    public function setContent(?string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;

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

    public function getTags(): ?string
    {
        return $this->tags;
    }

    public function setTags(?string $tags): static
    {
        $this->tags = $tags;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getFile(): ?string
    {
        return $this->file;
    }

    public function setFile(?string $file): static
    {
        $this->file = $file;

        return $this;
    }

    public function getSource(): ?string
    {
        return $this->source;
    }

    public function setSource(?string $source): static
    {
        $this->source = $source;

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
}
