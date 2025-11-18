<?php
// src/Entity/Calendar.php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity]
class Calendar
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column(type:"integer")]
    private int $id;

    #[ORM\Column(type:"string", length:255)]
    private string $name;

    #[ORM\Column(type:"text", nullable:true)]
    private ?string $description = null;

    #[ORM\Column(type:"string", length:7, nullable:true)]
    private ?string $color = null; // z.B. Hex-Farbe #FF0000

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: "calendars")]
    private User $owner;

    #[ORM\OneToMany(targetEntity: CalendarEvent::class, mappedBy: "calendar", cascade:["persist", "remove"])]
    private Collection $events;

    public function __construct() {
        $this->events = new ArrayCollection();
    }

    // ... Getter & Setter ...

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(?string $color): static
    {
        $this->color = $color;

        return $this;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): static
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * @return Collection<int, CalendarEvent>
     */
    public function getEvents(): Collection
    {
        return $this->events;
    }

    public function addEvent(CalendarEvent $event): static
    {
        if (!$this->events->contains($event)) {
            $this->events->add($event);
            $event->setCalendar($this);
        }

        return $this;
    }

    public function removeEvent(CalendarEvent $event): static
    {
        if ($this->events->removeElement($event)) {
            // set the owning side to null (unless already changed)
            if ($event->getCalendar() === $this) {
                $event->setCalendar(null);
            }
        }

        return $this;
    }
}
