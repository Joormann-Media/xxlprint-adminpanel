<?php

namespace App\Entity;

use App\Repository\WorkLogRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: WorkLogRepository::class)]
class WorkLog
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

   #[ORM\ManyToOne(inversedBy: 'workLogs')]
    #[ORM\JoinColumn(nullable: false)]
    private Employee $employee;

    #[ORM\Column(type: 'datetime')]
    private \DateTime $clockInAt;

    #[ORM\Column(type: 'string', length: 100)]
    private string $location;

    #[ORM\Column(type: 'string', length: 50)]
    private string $method;

    #[ORM\Column(type: 'string', length: 255)]
    private string $deviceUid;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private ?string $source = null;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'integer', options: ['default' => 0])]
    private int $clockIn = 0;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getClockInAt(): ?\DateTime
    {
        return $this->clockInAt;
    }

    public function setClockInAt(\DateTime $clockInAt): static
    {
        $this->clockInAt = $clockInAt;

        return $this;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(string $location): static
    {
        $this->location = $location;

        return $this;
    }

    public function getMethod(): ?string
    {
        return $this->method;
    }

    public function setMethod(string $method): static
    {
        $this->method = $method;

        return $this;
    }

    public function getDeviceUid(): ?string
    {
        return $this->deviceUid;
    }

    public function setDeviceUid(string $deviceUid): static
    {
        $this->deviceUid = $deviceUid;

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

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getClockIn(): int
    {
        return $this->clockIn;
    }

    public function setClockIn(int $clockIn): static
    {
        $this->clockIn = ($clockIn === 1) ? 1 : 0;
        return $this;
    }

    public function getEmployee(): ?Employee
    {
        return $this->employee;
    }

    public function setEmployee(?Employee $employee): static
    {
        $this->employee = $employee;

        return $this;
    }
}

