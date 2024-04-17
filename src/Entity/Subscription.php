<?php

namespace App\Entity;

use App\Repository\SubscriptionRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'subscription')]
#[ORM\Entity(repositoryClass: SubscriptionRepository::class)]
#[ORM\Index(name: 'subscription__student_id__ind', columns: ['student_id'])]
#[ORM\Index(name: 'subscription__course_id__ind', columns: ['course_id'])]
#[ORM\HasLifecycleCallbacks]
class Subscription
{

    #[ORM\Column(name: 'id', type: 'integer', unique: true)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private ?int $id = null;

    #[ORM\Column(name: 'created_at', type: 'datetime', nullable: false)]
    private DateTime $createdAt;

    #[ORM\Column(name: 'updated_at', type: 'datetime', nullable: false)]
    private DateTime $updatedAt;

    #[ORM\ManyToOne(targetEntity: Student::class, inversedBy: 'subscriptions')]
    #[ORM\JoinColumn(name: 'student_id', referencedColumnName: 'id')]
    private Student $student;

    #[ORM\ManyToOne(targetEntity: Course::class, inversedBy: 'subscriptions')]
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
     * @return Subscription
     */
    public function setId(?int $id): Subscription
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
     * @return Subscription
     */
    #[ORM\PrePersist]
    public function setCreatedAt(): Subscription
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
     * @return Subscription
     */
    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function setUpdatedAt(): Subscription
    {
        $this->updatedAt = new DateTime();
        return $this;
    }

    /**
     * @return Student
     */
    public function getStudent(): Student
    {
        return $this->student;
    }

    /**
     * @param Student $student
     * @return Subscription
     */
    public function setStudent(Student $student): Subscription
    {
        $this->student = $student;
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
     * @return Subscription
     */
    public function setCourse(Course $course): Subscription
    {
        $this->course = $course;
        return $this;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'createdAt' => $this->getCreatedAt(),
            'updatedAt' => $this->getUpdatedAt(),
            'student' => $this->getStudent()->toArray(),
            'course' => $this->getCourse()->toArray()
        ];
    }
}
