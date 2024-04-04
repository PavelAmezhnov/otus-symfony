<?php

namespace App\Entity;

use App\Repository\CompletedTaskRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table(name: 'completed_task')]
#[ORM\Entity(repositoryClass: CompletedTaskRepository::class)]
#[ORM\Index(name: 'completed_task__student_id__ind', columns: ['student_id'])]
#[ORM\Index(name: 'completed_task__task_id__ind', columns: ['task_id'])]
#[ORM\Index(name: 'completed_task__grade__ind', columns: ['grade'])]
#[ORM\HasLifecycleCallbacks]
class CompletedTask
{

    #[ORM\Column(name: 'id', type: 'integer', unique: true)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private ?int $id = null;

    #[ORM\Column(name: 'created_at', type: 'datetime', nullable: false)]
    private DateTime $createdAt;

    #[ORM\Column(name: 'updated_at', type: 'datetime', nullable: false)]
    private DateTime $updatedAt;

    #[ORM\Column(name: 'finished_at', type: 'datetime', nullable: true)]
    private ?DateTime $finishedAt;

    #[ORM\Column(name: 'grade', type: 'smallint', nullable: true)]
    #[Assert\Range(min: 1, max: 10)]
    private ?int $grade;

    #[ORM\ManyToOne(targetEntity: Student::class, inversedBy: 'completedTasks')]
    #[ORM\JoinColumn(name: 'student_id', referencedColumnName: 'id')]
    private Student $student;

    #[ORM\ManyToOne(targetEntity: Task::class, inversedBy: 'completedTasks')]
    #[ORM\JoinColumn(name: 'task_id', referencedColumnName: 'id')]
    private Task $task;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int|null $id
     * @return CompletedTask
     */
    public function setId(?int $id): CompletedTask
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
     * @return CompletedTask
     */
    #[ORM\PrePersist]
    public function setCreatedAt(): CompletedTask
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
     * @return CompletedTask
     */
    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function setUpdatedAt(): CompletedTask
    {
        $this->updatedAt = new DateTime();
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getFinishedAt(): DateTime
    {
        return $this->finishedAt;
    }

    /**
     * @return CompletedTask
     */
    public function setFinishedAt(): CompletedTask
    {
        $this->finishedAt = new DateTime();
        return $this;
    }

    /**
     * @return int|null
     */
    public function getGrade(): ?int
    {
        return $this->grade;
    }

    /**
     * @param int $grade
     * @return CompletedTask
     */
    public function setGrade(int $grade): CompletedTask
    {
        $this->grade = $grade;
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
     * @return CompletedTask
     */
    public function setStudent(Student $student): CompletedTask
    {
        $this->student = $student;
        return $this;
    }

    /**
     * @return Task
     */
    public function getTask(): Task
    {
        return $this->task;
    }

    /**
     * @param Task $task
     * @return CompletedTask
     */
    public function setTask(Task $task): CompletedTask
    {
        $this->task = $task;
        return $this;
    }
}
