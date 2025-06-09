<?php

namespace App\Entity;

use App\Repository\CourseRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CourseRepository::class)]
#[ORM\Table(name: 'course')]
#[ORM\Index(name: 'idx_slug', columns: ['slug'])]
class Course
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'courses')]
    #[ORM\JoinColumn(nullable: false)]
    private ?TutorProfile $tutor = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $slug = null;

    #[ORM\Column(type: 'text')]
    private ?string $description = null;

    #[ORM\Column(type: 'decimal', precision: 8, scale: 2)]
    private ?string $price = null;

    #[ORM\Column]
    private ?int $duration = null;

    #[ORM\Column]
    private bool $isActive = true;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\OneToMany(mappedBy: 'course', targetEntity: Booking::class)]
    private Collection $bookings;

    #[ORM\ManyToMany(targetEntity: Subject::class, inversedBy: 'courses')]
    private Collection $subjects;

    #[ORM\ManyToMany(targetEntity: FileMetadata::class, inversedBy: 'courses')]
    private Collection $materials;

    public function __construct()
    {
        $this->bookings = new ArrayCollection();
        $this->subjects = new ArrayCollection();
        $this->materials = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
    }

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

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;
        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = $slug;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;
        return $this;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(string $price): static
    {
        $this->price = $price;
        return $this;
    }

    public function getDuration(): ?int
    {
        return $this->duration;
    }

    public function setDuration(int $duration): static
    {
        $this->duration = $duration;
        return $this;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): static
    {
        $this->isActive = $isActive;
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
            $booking->setCourse($this);
        }
        return $this;
    }

    public function removeBooking(Booking $booking): static
    {
        if ($this->bookings->removeElement($booking)) {
            if ($booking->getCourse() === $this) {
                $booking->setCourse(null);
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

    /**
     * @return Collection<int, FileMetadata>
     */
    public function getMaterials(): Collection
    {
        return $this->materials;
    }

    public function addMaterial(FileMetadata $material): static
    {
        if (!$this->materials->contains($material)) {
            $this->materials->add($material);
        }
        return $this;
    }

    public function removeMaterial(FileMetadata $material): static
    {
        $this->materials->removeElement($material);
        return $this;
    }
}