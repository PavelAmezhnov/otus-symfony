<?php

namespace App\Entity;

use App\Repository\PercentageRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Table(name: 'percentage')]
#[ORM\Entity(repositoryClass: PercentageRepository::class)]
#[ORM\Index(name: 'percentage__task_id__ind', columns: ['task_id'])]
#[ORM\Index(name: 'percentage__skill_id__ind', columns: ['skill_id'])]
#[ORM\HasLifecycleCallbacks]
class Percentage
{
    #[ORM\Column(name: 'id', type: 'integer', unique: true)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private ?int $id = null;

    #[ORM\Column(name: 'created_at', type: 'datetime', nullable: false)]
    private DateTime $createdAt;

    #[ORM\Column(name: 'updated_at', type: 'datetime', nullable: false)]
    private DateTime $updatedAt;

    #[ORM\Column(name: 'percent', type: 'float', nullable: false)]
    #[Assert\Range(min: 0, max: 100)]
    private float $percent;

    #[ORM\ManyToOne(targetEntity: Task::class, inversedBy: 'percentages')]
    #[ORM\JoinColumn(name: 'task_id', referencedColumnName: 'id')]
    private Task $task;

    #[ORM\ManyToOne(targetEntity: Skill::class, inversedBy: 'percentages')]
    #[ORM\JoinColumn(name: 'skill_id', referencedColumnName: 'id')]
    private Skill $skill;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int|null $id
     * @return Percentage
     */
    public function setId(?int $id): Percentage
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
     * @return Percentage
     */
    #[ORM\PrePersist]
    public function setCreatedAt(): Percentage
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
     * @return Percentage
     */
    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function setUpdatedAt(): Percentage
    {
        $this->updatedAt = new DateTime();
        return $this;
    }

    /**
     * @return float
     */
    public function getPercent(): float
    {
        return $this->percent;
    }

    /**
     * @param float $percent
     * @return Percentage
     */
    public function setPercent(float $percent): Percentage
    {
        $this->percent = $percent;
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
     * @return Percentage
     */
    public function setTask(Task $task): Percentage
    {
        $this->task = $task;
        return $this;
    }

    /**
     * @return Skill
     */
    public function getSkill(): Skill
    {
        return $this->skill;
    }

    /**
     * @param Skill $skill
     * @return Percentage
     */
    public function setSkill(Skill $skill): Percentage
    {
        $this->skill = $skill;
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
            'task' => $this->getTask()->toArray(),
            'skill' => $this->getSkill()->toArray(),
            'percent' => $this->getPercent()
        ];
    }
}
