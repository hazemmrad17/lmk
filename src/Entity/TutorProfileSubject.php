<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'tutor_profile_subject')]
class TutorProfileSubject
{
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: TutorProfile::class)]
    #[ORM\JoinColumn(name: 'tutor_profile_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private ?TutorProfile $tutorProfile = null;

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Subject::class)]
    #[ORM\JoinColumn(name: 'subject_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private ?Subject $subject = null;

    public function getTutorProfile(): ?TutorProfile
    {
        return $this->tutorProfile;
    }

    public function setTutorProfile(?TutorProfile $tutorProfile): self
    {
        $this->tutorProfile = $tutorProfile;
        return $this;
    }

    public function getSubject(): ?Subject
    {
        return $this->subject;
    }

    public function setSubject(?Subject $subject): self
    {
        $this->subject = $subject;
        return $this;
    }
}