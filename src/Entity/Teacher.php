<?php

namespace App\Entity;

use App\Repository\TeacherRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\UniqueConstraint;

#[ORM\Table(name: 'teacher')]
#[ORM\Entity(repositoryClass: TeacherRepository::class)]
#[ORM\Index(name: 'teacher__last_name__first_name__ind', columns: ['last_name', 'first_name'])]
#[ORM\Index(name: 'teacher__first_name__last_name__ind', columns: ['first_name', 'last_name'])]
#[UniqueConstraint(name: 'teacher__user__uniq', fields: ['user'])]
#[ORM\HasLifecycleCallbacks]
class Teacher implements HasArrayRepresentation
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

    #[ORM\OneToMany(targetEntity: CuratedCourse::class, mappedBy: 'teacher', cascade: ['remove'])]
    private Collection $curatedCourses;

    #[ORM\OneToOne(targetEntity: User::class, cascade: ['remove'])]
    private User $user;

    public function __construct()
    {
        $this->curatedCourses = new ArrayCollection();
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
     * @return Teacher
     */
    public function setId(?int $id): Teacher
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
     * @return Teacher
     */
    #[ORM\PrePersist]
    public function setCreatedAt(): Teacher
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
     * @return Teacher
     */
    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function setUpdatedAt(): Teacher
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
     * @return Teacher
     */
    public function setFirstName(string $firstName): Teacher
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
     * @return Teacher
     */
    public function setLastName(?string $lastName): Teacher
    {
        $this->lastName = $lastName;
        return $this;
    }

    /**
     * @return Collection
     */
    public function getCuratedCourses(): Collection
    {
        return $this->curatedCourses;
    }

    /**
     * @param CuratedCourse $curatedCourse
     * @return Teacher
     */
    public function addCuratedCourses(CuratedCourse $curatedCourse): Teacher
    {
        if (!$this->curatedCourses->contains($curatedCourse)) {
            $this->curatedCourses->add($curatedCourse);
        }
        return $this;
    }

    /**
     * @param CuratedCourse $curatedCourse
     * @return Teacher
     */
    public function removeCuratedCourse(CuratedCourse $curatedCourse): Teacher
    {
        $this->curatedCourses->remove($curatedCourse);
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
     * @return Teacher
     */
    public function setUser(User $user): Teacher
    {
        $this->user = $user;
        return $this;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'createdAt' => $this->getCreatedAt(),
            'updatedAt' => $this->getUpdatedAt(),
            'firstName' => $this->getFirstName(),
            'lastName' => $this->getLastName(),
            'curatedCourses' => array_map(
                static fn(Subscription $s) => [
                    'id' => $s->getCourse()->getId(),
                    'name' => $s->getCourse()->getName()
                ],
                $this->getCuratedCourses()->toArray()
            )
        ];
    }
}
