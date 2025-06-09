<?php

namespace App\Entity;

use App\Repository\AvailabilitySlotRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AvailabilitySlotRepository::class)]
class AvailabilitySlot
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'availabilitySlots')]
    #[ORM\JoinColumn(nullable: false)]
    private ?TutorProfile $tutor = null;

    #[ORM\Column(length: 10)]
    private ?string $dayOfWeek = null;

    #[ORM\Column(type: 'time')]
    private ?\DateTimeInterface $startTime = null;

    #[ORM\Column(type: 'time')]
    private ?\DateTimeInterface $endTime = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTutor(): ?TutorProfile
    {
        return $this->tutor;
    }

    public function setTutor(?TutorProfile $tutor): static
    {
        $this->tutor = $tutor;
        return $this;
    }

    public function getDayOfWeek(): ?string
    {
        return $this->dayOfWeek;
    }

    public function setDayOfWeek(string $dayOfWeek): static
    {
        $this->dayOfWeek = $dayOfWeek;
        return $this;
    }

    public function getStartTime(): ?\DateTimeInterface
    {
        return $this->startTime;
    }

    public function setStartTime(\DateTimeInterface $startTime): static
    {
        $this->startTime = $startTime;
        return $this;
    }

    public function getEndTime(): ?\DateTimeInterface
    {
        return $this->endTime;
    }

    public function setEndTime(\DateTimeInterface $endTime): static
    {
        $this->endTime = $endTime;
        return $this;
    }
}