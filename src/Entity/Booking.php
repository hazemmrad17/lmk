<?php

namespace App\Entity;

use App\Repository\BookingRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BookingRepository::class)]
#[ORM\Table(name: 'booking')]
#[ORM\Index(name: 'idx_booking_status', columns: ['status'])]
class Booking
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'bookings')]
    #[ORM\JoinColumn(nullable: false)]
    private ?StudentProfile $student = null;

    #[ORM\ManyToOne(inversedBy: 'bookings')]
    #[ORM\JoinColumn(nullable: false)]
    private ?TutorProfile $tutor = null;

    #[ORM\ManyToOne(inversedBy: 'bookings')]
    private ?Course $course = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $startTime = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $endTime = null;

    #[ORM\Column(length: 50)]
    private ?string $status = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\OneToOne(mappedBy: 'booking', cascade: ['persist', 'remove'])]
    private ?ChatThread $chatThread = null;

    #[ORM\OneToOne(mappedBy: 'booking', cascade: ['persist', 'remove'])]
    private ?Payment $payment = null;

    #[ORM\OneToOne(mappedBy: 'booking', cascade: ['persist', 'remove'])]
    private ?Review $review = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStudent(): ?StudentProfile
    {
        return $this->student;
    }

    public function setStudent(?StudentProfile $student): static
    {
        $this->student = $student;
        return $this;
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

    public function getCourse(): ?Course
    {
        return $this->course;
    }

    public function setCourse(?Course $course): static
    {
        $this->course = $course;
        return $this;
    }

    public function getStartTime(): ?\DateTimeImmutable
    {
        return $this->startTime;
    }

    public function setStartTime(\DateTimeImmutable $startTime): static
    {
        $this->startTime = $startTime;
        return $this;
    }

    public function getEndTime(): ?\DateTimeImmutable
    {
        return $this->endTime;
    }

    public function setEndTime(\DateTimeImmutable $endTime): static
    {
        $this->endTime = $endTime;
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

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getChatThread(): ?ChatThread
    {
        return $this->chatThread;
    }

    public function setChatThread(?ChatThread $chatThread): static
    {
        if ($chatThread === null && $this->chatThread !== null) {
            $this->chatThread->setBooking(null);
        }

        if ($chatThread !== null && $chatThread->getBooking() !== $this) {
            $chatThread->setBooking($this);
        }

        $this->chatThread = $chatThread;
        return $this;
    }

    public function getPayment(): ?Payment
    {
        return $this->payment;
    }

    public function setPayment(?Payment $payment): static
    {
        if ($payment === null && $this->payment !== null) {
            $this->payment->setBooking(null);
        }

        if ($payment !== null && $payment->getBooking() !== $this) {
            $payment->setBooking($this);
        }

        $this->payment = $payment;
        return $this;
    }

    public function getReview(): ?Review
    {
        return $this->review;
    }

    public function setReview(?Review $review): static
    {
        if ($review === null && $this->review !== null) {
            $this->review->setBooking(null);
        }

        if ($review !== null && $review->getBooking() !== $this) {
            $review->setBooking($this);
        }

        $this->review = $review;
        return $this;
    }
}