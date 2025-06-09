<?php

namespace App\Entity;

use App\Repository\SubjectRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SubjectRepository::class)]
class Subject
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $name = null;

    #[ORM\ManyToMany(targetEntity: TutorProfile::class, mappedBy: 'subjects')]
    private Collection $tutorProfiles;

    #[ORM\ManyToMany(targetEntity: Course::class, mappedBy: 'subjects')]
    private Collection $courses;

    public function __construct()
    {
        $this->tutorProfiles = new ArrayCollection();
        $this->courses = new ArrayCollection();
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

    /**
     * @return Collection<int, TutorProfile>
     */
    public function getTutorProfiles(): Collection
    {
        return $this->tutorProfiles;
    }

    public function addTutorProfile(TutorProfile $tutorProfile): static
    {
        if (!$this->tutorProfiles->contains($tutorProfile)) {
            $this->tutorProfiles->add($tutorProfile);
            $tutorProfile->addSubject($this);
        }
        return $this;
    }

    public function removeTutorProfile(TutorProfile $tutorProfile): static
    {
        if ($this->tutorProfiles->removeElement($tutorProfile)) {
            $tutorProfile->removeSubject($this);
        }
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
            $course->addSubject($this);
        }
        return $this;
    }

    public function removeCourse(Course $course): static
    {
        if ($this->courses->removeElement($course)) {
            $course->removeSubject($this);
        }
        return $this;
    }
}