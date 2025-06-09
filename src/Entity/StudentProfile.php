<?php

namespace App\Entity;

use App\Repository\StudentProfileRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StudentProfileRepository::class)]
class StudentProfile
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'studentProfile')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Profile $profile = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $gradeLevel = null;

    #[ORM\Column(length: 50)]
    private string $trialStatus = 'none';

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $parentEmail = null;

    #[ORM\OneToMany(mappedBy: 'student', targetEntity: Booking::class, orphanRemoval: true)]
    private Collection $bookings;

    #[ORM\OneToMany(mappedBy: 'student', targetEntity: Review::class, orphanRemoval: true)]
    private Collection $reviews;

    public function __construct()
    {
        $this->bookings = new ArrayCollection();
        $this->reviews = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProfile(): ?Profile
    {
        return $this->profile;
    }

    public function setProfile(Profile $profile): static
    {
        $this->profile = $profile;
        return $this;
    }

    public function getGradeLevel(): ?string
    {
        return $this->gradeLevel;
    }

    public function setGradeLevel(?string $gradeLevel): static
    {
        $this->gradeLevel = $gradeLevel;
        return $this;
    }

    public function getTrialStatus(): string
    {
        return $this->trialStatus;
    }

    public function setTrialStatus(string $trialStatus): static
    {
        $this->trialStatus = $trialStatus;
        return $this;
    }

    public function getParentEmail(): ?string
    {
        return $this->parentEmail;
    }

    public function setParentEmail(?string $parentEmail): static
    {
        $this->parentEmail = $parentEmail;
        return $this;
    }

    /**
     * @return Collection<int, Booking>
     */
    public function getBookings(): Collection
    {
        return $this->bookings;
    }

    public function addBooking(Booking $booking): static
    {
        if (!$this->bookings->contains($booking)) {
            $this->bookings->add($booking);
            $booking->setStudent($this);
        }
        return $this;
    }

    public function removeBooking(Booking $booking): static
    {
        if ($this->bookings->removeElement($booking)) {
            if ($booking->getStudent() === $this) {
                $booking->setStudent(null);
            }
        }
        return $this;
    }

    /**
     * @return Collection<int, Review>
     */
    public function getReviews(): Collection
    {
        return $this->reviews;
    }

    public function addReview(Review $review): static
    {
        if (!$this->reviews->contains($review)) {
            $this->reviews->add($review);
            $review->setStudent($this);
        }
        return $this;
    }

    public function removeReview(Review $review): static
    {
        if ($this->reviews->removeElement($review)) {
            if ($review->getStudent() === $this) {
                $review->setStudent(null);
            }
        }
        return $this;
    }
}