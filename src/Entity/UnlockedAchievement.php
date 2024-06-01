<?php

namespace App\Entity;

use App\Repository\UnlockedAchievementRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\UniqueConstraint;

#[ORM\Table(name: 'unlocked_achievement')]
#[ORM\Entity(repositoryClass: UnlockedAchievementRepository::class)]
#[ORM\Index(name: 'unlocked_achievement__student_id__ind', columns: ['student_id'])]
#[ORM\Index(name: 'unlocked_achievement__achievement_id__ind', columns: ['achievement_id'])]
#[UniqueConstraint(name: 'unlocked_achievement__student__achievement__uniq', fields: ['student', 'achievement'])]
#[ORM\HasLifecycleCallbacks]
class UnlockedAchievement implements HasArrayRepresentation
{
    #[ORM\Column(name: 'id', type: 'integer', unique: true)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private ?int $id = null;

    #[ORM\Column(name: 'created_at', type: 'datetime', nullable: false)]
    private DateTime $createdAt;

    #[ORM\Column(name: 'updated_at', type: 'datetime', nullable: false)]
    private DateTime $updatedAt;

    #[ORM\ManyToOne(targetEntity: Student::class, inversedBy: 'unlockedAchievements')]
    #[ORM\JoinColumn(name: 'student_id', referencedColumnName: 'id')]
    private Student $student;

    #[ORM\ManyToOne(targetEntity: Achievement::class, inversedBy: 'unlockedAchievements')]
    #[ORM\JoinColumn(name: 'achievement_id', referencedColumnName: 'id')]
    private Achievement $achievement;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int|null $id
     * @return UnlockedAchievement
     */
    public function setId(?int $id): UnlockedAchievement
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
     * @return UnlockedAchievement
     */
    #[ORM\PrePersist]
    public function setCreatedAt(): UnlockedAchievement
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
     * @return UnlockedAchievement
     */
    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function setUpdatedAt(): UnlockedAchievement
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
     * @return $this
     */
    public function setStudent(Student $student): UnlockedAchievement
    {
        $this->student = $student;
        return $this;
    }

    /**
     * @return Achievement
     */
    public function getAchievement(): Achievement
    {
        return $this->achievement;
    }

    /**
     * @param Achievement $achievement
     * @return $this
     */
    public function setAchievement(Achievement $achievement): UnlockedAchievement
    {
        $this->achievement = $achievement;
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
            'achievement' => $this->getAchievement()->toArray()
        ];
    }
}
