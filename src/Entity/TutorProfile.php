<?php

namespace App\Entity;

use App\Repository\TutorProfileRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TutorProfileRepository::class)]
class TutorProfile
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'tutorProfile')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Profile $profile = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $bio = null;

    #[ORM\Column(type: 'decimal', precision: 8, scale: 2)]
    private ?string $hourlyRate = null;

    #[ORM\OneToMany(mappedBy: 'tutor', targetEntity: Course::class, orphanRemoval: true)]
    private Collection $courses;

    #[ORM\OneToMany(mappedBy: 'tutor', targetEntity: Booking::class, orphanRemoval: true)]
    private Collection $bookings;

    #[ORM\OneToMany(mappedBy: 'tutor', targetEntity: Review::class, orphanRemoval: true)]
    private Collection $reviews;

    #[ORM\OneToMany(mappedBy: 'tutor', targetEntity: AvailabilitySlot::class, orphanRemoval: true)]
    private Collection $availabilitySlots;

    #[ORM\ManyToMany(targetEntity: Subject::class, inversedBy: 'tutorProfiles')]
    private Collection $subjects;

    public function __construct()
    {
        $this->courses = new ArrayCollection();
        $this->bookings = new ArrayCollection();
        $this->reviews = new ArrayCollection();
        $this->availabilitySlots = new ArrayCollection();
        $this->subjects = new ArrayCollection();
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

    public function getBio(): ?string
    {
        return $this->bio;
    }

    public function setBio(?string $bio): static
    {
        $this->bio = $bio;
        return $this;
    }

    public function getHourlyRate(): ?string
    {
        return $this->hourlyRate;
    }

    public function setHourlyRate(string $hourlyRate): static
    {
        $this->hourlyRate = $hourlyRate;
        return $this;
    }

    /**
     * @return Collection<int, Course>
     */
    public function getCourses(): Collection
    {
        return $this->courses;
    }

    public function addCourse(Course $course): static
    {
        if (!$this->courses->contains($course)) {
            $this->courses->add($course);
            $course->setTutor($this);
        }
        return $this;
    }

    public function removeCourse(Course $course): static
    {
        if ($this->courses->removeElement($course)) {
            if ($course->getTutor() === $this) {
                $course->setTutor(null);
            }
        }
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
            $booking->setTutor($this);
        }
        return $this;
    }

    public function removeBooking(Booking $booking): static
    {
        if ($this->bookings->removeElement($booking)) {
            if ($booking->getTutor() === $this) {
                $booking->setTutor(null);
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
            $review->setTutor($this);
        }
        return $this;
    }

    public function removeReview(Review $review): static
    {
        if ($this->reviews->removeElement($review)) {
            if ($review->getTutor() === $this) {
                $review->setTutor(null);
            }
        }
        return $this;
    }

    /**
     * @return Collection<int, AvailabilitySlot>
     */
    public function getAvailabilitySlots(): Collection
    {
        return $this->availabilitySlots;
    }

    public function addAvailabilitySlot(AvailabilitySlot $availabilitySlot): static
    {
        if (!$this->availabilitySlots->contains($availabilitySlot)) {
            $this->availabilitySlots->add($availabilitySlot);
            $availabilitySlot->setTutor($this);
        }
        return $this;
    }

    public function removeAvailabilitySlot(AvailabilitySlot $availabilitySlot): static
    {
        if ($this->availabilitySlots->removeElement($availabilitySlot)) {
            if ($availabilitySlot->getTutor() === $this) {
                $availabilitySlot->setTutor(null);
            }
        }
        return $this;
    }

    /**
     * @return Collection<int, Subject>
     */
    public function getSubjects(): Collection
    {
        return $this->subjects;
    }

    public function addSubject(Subject $subject): static
    {
        if (!$this->subjects->contains($subject)) {
            $this->subjects->add($subject);
        }
        return $this;
    }

    public function removeSubject(Subject $subject): static
    {
        $this->subjects->removeElement($subject);
        return $this;
    }
}