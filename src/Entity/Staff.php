<?php

namespace App\Entity;

use App\Repository\StaffRepository;
use DateTime;
use Doctrine\ORM\Mapping\UniqueConstraint;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'staff')]
#[ORM\Entity(repositoryClass: StaffRepository::class)]
#[ORM\Index(name: 'staff__last_name__first_name__ind', columns: ['last_name', 'first_name'])]
#[ORM\Index(name: 'staff__first_name__last_name__ind', columns: ['first_name', 'last_name'])]
#[UniqueConstraint(name: 'staff__user__uniq', fields: ['user'])]
#[ORM\HasLifecycleCallbacks]
class Staff implements HasArrayRepresentation
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

    #[ORM\OneToOne(targetEntity: User::class, cascade: ['remove'])]
    private User $user;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int|null $id
     * @return Staff
     */
    public function setId(?int $id): Staff
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
     * @return Staff
     */
    #[ORM\PrePersist]
    public function setCreatedAt(): Staff
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
     * @return Staff
     */
    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function setUpdatedAt(): Staff
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
     * @return Staff
     */
    public function setFirstName(string $firstName): Staff
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
     * @return Staff
     */
    public function setLastName(?string $lastName): Staff
    {
        $this->lastName = $lastName;
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
     * @return Staff
     */
    public function setUser(User $user): Staff
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
            'user' => $this->getUser()->toArray()
        ];
    }
}
