<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'course_material')]
class CourseMaterial
{
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Course::class)]
    #[ORM\JoinColumn(name: 'course_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private ?Course $course = null;

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: FileMetadata::class)]
    #[ORM\JoinColumn(name: 'file_metadata_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private ?FileMetadata $fileMetadata = null;

    public function getCourse(): ?Course
    {
        return $this->course;
    }

    public function setCourse(?Course $course): self
    {
        $this->course = $course;
        return $this;
    }

    public function getFileMetadata(): ?FileMetadata
    {
        return $this->fileMetadata;
    }

    public function setFileMetadata(?FileMetadata $fileMetadata): self
    {
        $this->fileMetadata = $fileMetadata;
        return $this;
    }
}