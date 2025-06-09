<?php

namespace App\Entity;

use App\Repository\ProfileRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProfileRepository::class)]
class Profile
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'profile')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne]
    private ?FileMetadata $avatar = null;

    #[ORM\Column(length: 50)]
    private ?string $type = null;

    #[ORM\OneToOne(mappedBy: 'profile', cascade: ['persist', 'remove'])]
    private ?StudentProfile $studentProfile = null;

    #[ORM\OneToOne(mappedBy: 'profile', cascade: ['persist', 'remove'])]
    private ?TutorProfile $tutorProfile = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): static
    {
        $this->user = $user;
        return $this;
    }

    public function getAvatar(): ?FileMetadata
    {
        return $this->avatar;
    }

    public function setAvatar(?FileMetadata $avatar): static
    {
        $this->avatar = $avatar;
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

    public function getStudentProfile(): ?StudentProfile
    {
        return $this->studentProfile;
    }

    public function setStudentProfile(?StudentProfile $studentProfile): static
    {
        if ($studentProfile === null && $this->studentProfile !== null) {
            $this->studentProfile->setProfile(null);
        }

        if ($studentProfile !== null && $studentProfile->getProfile() !== $this) {
            $studentProfile->setProfile($this);
        }

        $this->studentProfile = $studentProfile;
        return $this;
    }

    public function getTutorProfile(): ?TutorProfile
    {
        return $this->tutorProfile;
    }

    public function setTutorProfile(?TutorProfile $tutorProfile): static
    {
        if ($tutorProfile === null && $this->tutorProfile !== null) {
            $this->tutorProfile->setProfile(null);
        }

        if ($tutorProfile !== null && $tutorProfile->getProfile() !== $this) {
            $tutorProfile->setProfile($this);
        }

        $this->tutorProfile = $tutorProfile;
        return $this;
    }
}