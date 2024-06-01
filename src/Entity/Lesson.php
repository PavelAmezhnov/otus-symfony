<?php

namespace App\Entity;

use App\Repository\LessonRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\UniqueConstraint;

#[ORM\Table(name: 'lesson')]
#[ORM\Entity(repositoryClass: LessonRepository::class)]
#[ORM\Index(name: 'lesson__course_id__ind', columns: ['course_id'])]
#[UniqueConstraint(name: 'lesson__name__course__uniq', fields: ['name', 'course'])]
#[ORM\HasLifecycleCallbacks]
class Lesson implements HasArrayRepresentation
{

    #[ORM\Column(name: 'id', type: 'integer', unique: true)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private ?int $id = null;

    #[ORM\Column(name: 'created_at', type: 'datetime', nullable: false)]
    private DateTime $createdAt;

    #[ORM\Column(name: 'updated_at', type: 'datetime', nullable: false)]
    private DateTime $updatedAt;

    #[ORM\Column(name: 'name', type: 'string', length: 128, nullable: false)]
    private string $name;

    #[ORM\ManyToOne(targetEntity: Course::class, inversedBy: 'lessons')]
    #[ORM\JoinColumn(name: 'course_id', referencedColumnName: 'id')]
    private ?Course $course = null;

    #[ORM\OneToMany(targetEntity: Task::class, mappedBy: 'lesson', cascade: ['remove'])]
    private Collection $tasks;

    public function __construct()
    {
        $this->tasks = new ArrayCollection();
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int|null $id
     * @return Lesson
     */
    public function setId(?int $id): Lesson
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
     * @return Lesson
     */
    #[ORM\PrePersist]
    public function setCreatedAt(): Lesson
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
     * @return Lesson
     */
    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function setUpdatedAt(): Lesson
    {
        $this->updatedAt = new DateTime();
        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Lesson
     */
    public function setName(string $name): Lesson
    {
        $this->name = $name;
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
     * @return Lesson
     */
    public function setCourse(Course $course): Lesson
    {
        $this->course = $course;
        return $this;
    }

    /**
     * @return Collection
     */
    public function getTasks(): Collection
    {
        return $this->tasks;
    }

    /**
     * @param Task $task
     * @return Lesson
     */
    public function addTask(Task $task): Lesson
    {
        if (!$this->tasks->contains($task)) {
            $this->tasks->add($task);
        }

        return $this;
    }

    /**
     * @param Task $task
     * @return Lesson
     */
    public function removeTask(Task $task): Lesson
    {
        $this->tasks->removeElement($task);
        return $this;
    }

    /**
     * @return Lesson
     */
    public function removeCourse(): Lesson
    {
        $this->course = null;
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
            'name' => $this->getName(),
            'course' => $this->getCourse()->toArray(),
            'tasks' => array_map(
                static fn(Task $t) => [
                    'id' => $t->getId(),
                    'name' => $t->getName()
                ],
                $this->getTasks()->toArray()
            )
        ];
    }
}
