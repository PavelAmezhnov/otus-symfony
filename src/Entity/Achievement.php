<?php

namespace App\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'achievement')]
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
class Achievement
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

    #[ORM\OneToMany(targetEntity: UnlockedAchievement::class, mappedBy: 'achievement')]
    private Collection $unlockedAchievements;

    public function __construct()
    {
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
     * @return Achievement
     */
    public function setId(?int $id): Achievement
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
     * @return Achievement
     */
    #[ORM\PrePersist]
    public function setCreatedAt(): Achievement
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
     * @return Achievement
     */
    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function setUpdatedAt(): Achievement
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
     * @return Achievement
     */
    public function setName(string $name): Achievement
    {
        $this->name = $name;
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
    public function addUnlockedAchievement(UnlockedAchievement $unlockedAchievement): Achievement
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
    public function removeUnlockedAchievement(UnlockedAchievement $unlockedAchievement): Achievement
    {
        $this->unlockedAchievements->removeElement($unlockedAchievement);
        return $this;
    }
}
