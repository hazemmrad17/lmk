<?php

namespace App\Entity;

use App\Repository\FileMetadataRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FileMetadataRepository::class)]
#[ORM\Table(name: 'file_metadata')]
class FileMetadata
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $path = null;

    #[ORM\Column(length: 50)]
    private ?string $type = null;

    #[ORM\ManyToOne(inversedBy: 'uploadedFiles')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $uploadedBy = null;

    #[ORM\Column]
    private ?int $size = null;

    #[ORM\Column(length: 100)]
    private ?string $mimeType = null;

    #[ORM\OneToMany(mappedBy: 'avatar', targetEntity: Profile::class)]
    private Collection $profiles;

    #[ORM\ManyToMany(targetEntity: Course::class, mappedBy: 'materials')]
    private Collection $courses;

    public function __construct()
    {
        $this->profiles = new ArrayCollection();
        $this->courses = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(string $path): static
    {
        $this->path = $path;
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

    public function getUploadedBy(): ?User
    {
        return $this->uploadedBy;
    }

    public function setUploadedBy(?User $uploadedBy): static
    {
        $this->uploadedBy = $uploadedBy;
        return $this;
    }

    public function getSize(): ?int
    {
        return $this->size;
    }

    public function setSize(int $size): static
    {
        $this->size = $size;
        return $this;
    }

    public function getMimeType(): ?string
    {
        return $this->mimeType;
    }

    public function setMimeType(string $mimeType): static
    {
        $this->mimeType = $mimeType;
        return $this;
    }

    /**
     * @return Collection<int, Profile>
     */
    public function getProfiles(): Collection
    {
        return $this->profiles;
    }

    public function addProfile(Profile $profile): static
    {
        if (!$this->profiles->contains($profile)) {
            $this->profiles->add($profile);
            $profile->setAvatar($this);
        }
        return $this;
    }

    public function removeProfile(Profile $profile): static
    {
        if ($this->profiles->removeElement($profile)) {
            if ($profile->getAvatar() === $this) {
                $profile->setAvatar(null);
            }
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
            $course->addMaterial($this);
        }
        return $this;
    }

    public function removeCourse(Course $course): static
    {
        if ($this->courses->removeElement($course)) {
            $course->removeMaterial($this);
        }
        return $this;
    }
}