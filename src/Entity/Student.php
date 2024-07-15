<?php

namespace App\Entity;

use App\Repository\StudentRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\UniqueConstraint;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Table(name: 'student')]
#[ORM\Entity(repositoryClass: StudentRepository::class)]
#[ORM\Index(name: 'student__last_name__first_name__ind', columns: ['last_name', 'first_name'])]
#[ORM\Index(name: 'student__first_name__last_name__ind', columns: ['first_name', 'last_name'])]
#[UniqueConstraint(name: 'student__user__uniq', fields: ['user'])]
#[ORM\HasLifecycleCallbacks]
class Student implements HasArrayRepresentation
{
    #[ORM\Column(name: 'id', type: 'integer', unique: true)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private ?int $id = null;

    #[ORM\Column(name: 'created_at', type: 'datetime', nullable: false)]
    private DateTime $createdAt;

    #[ORM\Column(name: 'updated_at', type: 'datetime', nullable: false)]
    private DateTime $updatedAt;

    #[ORM\Column(name: 'first_name', type: 'string', length: 64, nullable: false)]
    private string $firstName;

    #[ORM\Column(name: 'last_name', type: 'string', length: 64, nullable: true)]
    private ?string $lastName = null;

    #[ORM\OneToMany(targetEntity: Subscription::class, mappedBy: 'student', cascade: ['remove'])]
    private Collection $subscriptions;

    #[ORM\OneToMany(targetEntity: CompletedTask::class, mappedBy: 'student', cascade: ['remove'])]
    private Collection $completedTasks;

    #[ORM\OneToMany(targetEntity: UnlockedAchievement::class, mappedBy: 'student', cascade: ['remove'])]
    private Collection $unlockedAchievements;

    #[ORM\OneToOne(targetEntity: User::class, cascade: ['remove'])]
    private User $user;

    public function __construct()
    {
        $this->subscriptions = new ArrayCollection();
        $this->completedTasks = new ArrayCollection();
        $this->unlockedAchievements = new ArrayCollection();
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
     * @return Student
     */
    public function setId(?int $id): Student
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
     * @return Student
     */
    #[ORM\PrePersist]
    public function setCreatedAt(): Student
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
     * @return Student
     */
    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function setUpdatedAt(): Student
    {
        $this->updatedAt = new DateTime();
        return $this;
    }

    /**
     * @return string
     */
    public function getFirstName(): string
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     * @return Student
     */
    public function setFirstName(string $firstName): Student
    {
        $this->firstName = $firstName;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    /**
     * @param string|null $lastName
     * @return Student
     */
    public function setLastName(?string $lastName): Student
    {
        $this->lastName = $lastName;
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
    public function addSubscription(Subscription $subscription): Student
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
    public function removeSubscription(Subscription $subscription): Student
    {
        $this->subscriptions->removeElement($subscription);
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
     * @return $this
     */
    public function addCompletedTask(CompletedTask $completedTask): Student
    {
        if (!$this->completedTasks->contains($completedTask)) {
            $this->completedTasks->add($completedTask);
        }

        return $this;
    }

    /**
     * @param CompletedTask $completedTask
     * @return $this
     */
    public function removeCompletedTask(CompletedTask $completedTask): Student
    {
        $this->completedTasks->removeElement($completedTask);
        return $this;
    }

    /**
     * @return Collection
     */
    public function getUnlockedAchievements(): Collection
    {
        return $this->unlockedAchievements;
    }

    /**
     * @param UnlockedAchievement $unlockedAchievement
     * @return $this
     */
    public function addUnlockedAchievement(UnlockedAchievement $unlockedAchievement): Student
    {
        if (!$this->unlockedAchievements->contains($unlockedAchievement)) {
            $this->unlockedAchievements->add($unlockedAchievement);
        }

        return $this;
    }

    /**
     * @param UnlockedAchievement $unlockedAchievement
     * @return $this
     */
    public function removeUnlockedAchievement(UnlockedAchievement $unlockedAchievement): Student
    {
        $this->unlockedAchievements->removeElement($unlockedAchievement);
        return $this;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @param User $user
     * @return Student
     */
    public function setUser(UserInterface $user): Student
    {
        $this->user = $user;
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
            'firstName' => $this->getFirstName(),
            'lastName' => $this->getLastName(),
            'courses' => array_map(
                static fn(Subscription $s) => [
                    'id' => $s->getCourse()->getId(),
                    'name' => $s->getCourse()->getName()
                ],
                $this->getSubscriptions()->toArray()
            ),
            'unlockedAchievements' => array_map(
                static fn(UnlockedAchievement $ua) => [
                    'id' => $ua->getAchievement()->getId(),
                    'createdAt' => $ua->getCreatedAt(),
                    'name' => $ua->getAchievement()->getName()
                ],
                $this->getUnlockedAchievements()->toArray()
            )
        ];
    }
}
