<?php

namespace App\Entity;

use App\Repository\DownloadLogRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DownloadLogRepository::class)]
class DownloadLog
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: ReleaseFile::class)]
    private ReleaseFile $releaseFile;

    #[ORM\Column]
    private \DateTimeInterface $downloadedAt;

    #[ORM\Column(length: 45)]
    private string $ip;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $userAgent = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    private ?User $user = null;

    #[ORM\Column(length: 64, nullable: true)]
    private ?string $token = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getReleaseFile(): ReleaseFile
    {
        return $this->releaseFile;
    }

    public function setReleaseFile(ReleaseFile $releaseFile): self
    {
        $this->releaseFile = $releaseFile;
        return $this;
    }

    public function getDownloadedAt(): \DateTimeInterface
    {
        return $this->downloadedAt;
    }

    public function setDownloadedAt(\DateTimeInterface|string|null $downloadedAt = null): self
    {
        if (is_string($downloadedAt)) {
            $downloadedAt = new \DateTime($downloadedAt);
        }
        $this->downloadedAt = $downloadedAt ?? new \DateTime();
        return $this;
    }

    public function getIp(): string
    {
        return $this->ip;
    }

    public function setIp(string $ip): self
    {
        $this->ip = $ip;
        return $this;
    }

    public function getUserAgent(): ?string
    {
        return $this->userAgent;
    }

    public function setUserAgent(?string $userAgent): self
    {
        $this->userAgent = $userAgent;
        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;
        return $this;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(?string $token): self
    {
        $this->token = $token;
        return $this;
    }

    public function __toString(): string
    {
        $filename = $this->releaseFile->getOriginalFilename() ?? 'Unknown File';
        $downloadedAt = $this->downloadedAt instanceof \DateTimeInterface
            ? $this->downloadedAt->format('Y-m-d H:i:s')
            : 'Unknown Date';

        return sprintf('Download von %s am %s', $filename, $downloadedAt);
    }

}
