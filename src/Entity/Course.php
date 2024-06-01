<?php

namespace App\Entity;

use App\Repository\CourseRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\UniqueConstraint;

#[ORM\Table(name: 'course')]
#[ORM\Entity(repositoryClass: CourseRepository::class)]
#[UniqueConstraint(name: 'course__name__uniq', fields: ['name'])]
#[ORM\HasLifecycleCallbacks]
class Course implements HasArrayRepresentation
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

    #[ORM\OneToMany(targetEntity: Subscription::class, mappedBy: 'course', cascade: ['remove'])]
    private Collection $subscriptions;

    #[ORM\OneToMany(targetEntity: Lesson::class, mappedBy: 'course', cascade: ['remove'])]
    private Collection $lessons;

    public function __construct()
    {
        $this->subscriptions = new ArrayCollection();
        $this->lessons = new ArrayCollection();
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
     * @return Course
     */
    public function setId(?int $id): Course
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
     * @return Course
     */
    #[ORM\PrePersist]
    public function setCreatedAt(): Course
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
     * @return Course
     */
    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function setUpdatedAt(): Course
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
     * @return Course
     */
    public function setName(string $name): Course
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return Collection
     */
    public function getSubscriptions(): Collection
    {
        return $this->subscriptions;
    }

    /**
     * @param Subscription $subscription
     * @return $this
     */
    public function addSubscription(Subscription $subscription): Course
    {
        if (!$this->subscriptions->contains($subscription)) {
            $this->subscriptions->add($subscription);
        }

        return $this;
    }

    /**
     * @param Subscription $subscription
     * @return $this
     */
    public function removeSubscription(Subscription $subscription): Course
    {
        $this->subscriptions->removeElement($subscription);
        return $this;
    }

    /**
     * @return Collection
     */
    public function getLessons(): Collection
    {
        return $this->lessons;
    }

    /**
     * @param Lesson $lesson
     * @return Course
     */
    public function addLesson(Lesson $lesson): Course
    {
        if (!$this->lessons->contains($lesson)) {
            $this->lessons->add($lesson);
        }

        return $this;
    }

    /**
     * @param Lesson $lesson
     * @return Course
     */
    public function removeLesson(Lesson $lesson): Course
    {
        $this->lessons->removeElement($lesson);
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
            'lessons' => array_map(
                static fn(Lesson $l) => [
                    'id' => $l->getId(),
                    'name' =>  $l->getName()
                ],
                $this->getLessons()->toArray()
            )
        ];
    }
}
