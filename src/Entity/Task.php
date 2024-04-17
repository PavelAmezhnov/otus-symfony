<?php

namespace App\Entity;

use App\Repository\TaskRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Exception;

#[ORM\Table(name: 'task')]
#[ORM\Entity(repositoryClass: TaskRepository::class)]
#[ORM\Index(name: 'task__lesson_id__ind', columns: ['lesson_id'])]
#[ORM\HasLifecycleCallbacks]
class Task
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

    #[ORM\ManyToOne(targetEntity: Lesson::class, inversedBy: 'tasks')]
    #[ORM\JoinColumn(name: 'lesson_id', referencedColumnName: 'id')]
    private ?Lesson $lesson = null;

    #[ORM\OneToMany(targetEntity: Percentage::class, mappedBy: 'task')]
    private Collection $percentages;

    #[ORM\OneToMany(targetEntity: CompletedTask::class, mappedBy: 'task')]
    private Collection $completedTasks;

    public function __construct()
    {
        $this->percentages = new ArrayCollection();
        $this->completedTasks = new ArrayCollection();
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
     * @return Task
     */
    public function setId(?int $id): Task
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
     * @return Task
     */
    #[ORM\PrePersist]
    public function setCreatedAt(): Task
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
     * @return Task
     */
    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function setUpdatedAt(): Task
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
     * @return Task
     */
    public function setName(string $name): Task
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return Lesson
     */
    public function getLesson(): Lesson
    {
        return $this->lesson;
    }

    /**
     * @param Lesson $lesson
     * @return Task
     */
    public function setLesson(Lesson $lesson): Task
    {
        $this->lesson = $lesson;
        return $this;
    }

    /**
     * @return Collection
     */
    public function getPercentages(): Collection
    {
        return $this->percentages;
    }

    /**
     * @param Percentage $percentage
     * @return Task
     * @throws Exception
     */
    public function addPercentage(Percentage $percentage): Task
    {
        if (!$this->percentages->contains($percentage)) {
            $percents = 0;
            /** @var Percentage $p */
            foreach ($this->percentages as $p) {
                $percents += $p->getPercent();
            }

            if ($percents + $percentage->getPercent() > 100) {
                throw new Exception(sprintf(
                    'Доля добавляемого навыка не может превышать %s%%',
                    100 - $percents
                ));
            }

            $this->percentages->add($percentage);
        }

        return $this;
    }

    /**
     * @param Percentage $percentage
     * @return Task
     */
    public function removePercentage(Percentage $percentage): Task
    {
        $this->percentages->removeElement($percentage);
        return $this;
    }

    /**
     * @return Collection
     */
    public function getCompletedTasks(): Collection
    {
        return $this->completedTasks;
    }

    /**
     * @param CompletedTask $completedTask
     * @return Task
     */
    public function addCompletedTask(CompletedTask $completedTask): Task
    {
        if (!$this->completedTasks->contains($completedTask)) {
            $this->completedTasks->add($completedTask);
        }

        return $this;
    }

    /**
     * @param CompletedTask $completedTask
     * @return Task
     */
    public function removeCompletedTask(CompletedTask $completedTask): Task
    {
        $this->completedTasks->removeElement($completedTask);
        return $this;
    }

    /**
     * @return Task
     */
    public function removeLesson(): Task
    {
        $this->lesson = null;
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
            'lesson' => $this->getLesson()->toArray(),
            'skills' => array_map(
                static fn(Percentage $p) => [
                    'id' => $p->getSkill()->getId(),
                    'name' => $p->getSkill()->getName(),
                    'percent' => $p->getPercent()
                ],
                $this->getPercentages()->toArray()
            )
        ];
    }
}
