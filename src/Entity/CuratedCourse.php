<?php

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\UniqueConstraint;

#[ORM\Table(name: 'curated_course')]
#[ORM\Entity(repositoryClass: CuratedCourse::class)]
#[ORM\Index(name: 'curated_course__teacher_id__ind', columns: ['teacher_id'])]
#[ORM\Index(name: 'curated_course__course_id__ind', columns: ['course_id'])]
#[UniqueConstraint(name: 'curated_course__teacher__course__uniq', fields: ['teacher', 'course'])]
#[ORM\HasLifecycleCallbacks]
class CuratedCourse implements HasArrayRepresentation
{

    #[ORM\Column(name: 'id', type: 'integer', unique: true)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private ?int $id = null;

    #[ORM\Column(name: 'created_at', type: 'datetime', nullable: false)]
    private DateTime $createdAt;

    #[ORM\Column(name: 'updated_at', type: 'datetime', nullable: false)]
    private DateTime $updatedAt;

    #[ORM\ManyToOne(targetEntity: Teacher::class, inversedBy: 'curated_courses')]
    #[ORM\JoinColumn(name: 'teacher_id', referencedColumnName: 'id')]
    private Teacher $teacher;

    #[ORM\ManyToOne(targetEntity: Course::class, inversedBy: 'curated_courses')]
    #[ORM\JoinColumn(name: 'course_id', referencedColumnName: 'id')]
    private Course $course;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int|null $id
     * @return CuratedCourse
     */
    public function setId(?int $id): CuratedCourse
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    /**
     * @return CuratedCourse
     */
    #[ORM\PrePersist]
    public function setCreatedAt(): CuratedCourse
    {
        $this->createdAt = new DateTime();
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getUpdatedAt(): DateTime
    {
        return $this->updatedAt;
    }

    /**
     * @return CuratedCourse
     */
    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function setUpdatedAt(): CuratedCourse
    {
        $this->updatedAt = new DateTime();
        return $this;
    }

    /**
     * @return Teacher
     */
    public function getTeacher(): Teacher
    {
        return $this->teacher;
    }

    /**
     * @param Teacher $teacher
     * @return CuratedCourse
     */
    public function setStudent(Teacher $teacher): CuratedCourse
    {
        $this->teacher = $teacher;
        return $this;
    }

    /**
     * @return Course
     */
    public function getCourse(): Course
    {
        return $this->course;
    }

    /**
     * @param Course $course
     * @return CuratedCourse
     */
    public function setCourse(Course $course): CuratedCourse
    {
        $this->course = $course;
        return $this;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'createdAt' => $this->getCreatedAt(),
            'updatedAt' => $this->getUpdatedAt(),
            'teacher' => $this->getTeacher()->toArray(),
            'course' => $this->getCourse()->toArray()
        ];
    }
}
