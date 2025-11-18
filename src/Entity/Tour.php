<?php

namespace App\Entity;

use App\Repository\TourRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity(repositoryClass: TourRepository::class)]
class Tour
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $name = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    #[ORM\OneToMany(mappedBy: 'tour', targetEntity: TourSchedule::class, cascade: ['persist', 'remove'])]
    private Collection $schedules;
    #[ORM\ManyToMany(targetEntity: Schoolkids::class, inversedBy: 'tours')]
private Collection $schoolkids;

#[ORM\OneToMany(mappedBy: 'tour', targetEntity: StopPoint::class, cascade: ['persist', 'remove'])]
private Collection $stopPoints;
#[ORM\ManyToOne(targetEntity: School::class)]
#[ORM\JoinColumn(nullable: false)]
private ?School $school = null;

    public function __construct()
    {
        $this->schedules = new ArrayCollection();
        $this->schoolkids = new ArrayCollection();
        $this->stopPoints = new ArrayCollection();
    }

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

    /**
     * @return Collection<int, TourSchedule>
     */
    public function getSchedules(): Collection
    {
        return $this->schedules;
    }

    public function addSchedule(TourSchedule $schedule): static
    {
        if (!$this->schedules->contains($schedule)) {
            $this->schedules[] = $schedule;
            $schedule->setTour($this);
        }
        return $this;
    }

    public function removeSchedule(TourSchedule $schedule): static
    {
        if ($this->schedules->removeElement($schedule)) {
            // Set owning side to null (unless already changed)
            if ($schedule->getTour() === $this) {
                $schedule->setTour(null);
            }
        }
        return $this;
    }

    public function getSchoolkids(): Collection
{
    return $this->schoolkids;
}

public function addSchoolkid(Schoolkids $schoolkid): static
{
    if (!$this->schoolkids->contains($schoolkid)) {
        $this->schoolkids[] = $schoolkid;
        $schoolkid->addTour($this);
    }
    return $this;
}

public function removeSchoolkid(Schoolkids $schoolkid): static
{
    if ($this->schoolkids->removeElement($schoolkid)) {
        $schoolkid->removeTour($this);
    }
    return $this;
}

public function getStopPoints(): Collection
{
    return $this->stopPoints;
}

public function addStopPoint(StopPoint $stopPoint): static
{
    if (!$this->stopPoints->contains($stopPoint)) {
        $this->stopPoints[] = $stopPoint;
        $stopPoint->setTour($this);
    }
    return $this;
}

public function removeStopPoint(StopPoint $stopPoint): static
{
    if ($this->stopPoints->removeElement($stopPoint)) {
        if ($stopPoint->getTour() === $this) {
            $stopPoint->setTour(null);
        }
    }
    return $this;
}
// Getter/Setter nicht vergessen:
public function getSchool(): ?School { return $this->school; }
public function setSchool(?School $school): static { $this->school = $school; return $this; }
}
