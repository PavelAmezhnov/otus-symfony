<?php

namespace App\Entity;

use App\Repository\SkillRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: 'skill')]
#[ORM\Entity(repositoryClass: SkillRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Skill
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

    #[ORM\OneToMany(targetEntity: Percentage::class, mappedBy: 'skill')]
    private Collection $percentages;

    public function __construct()
    {
        $this->percentages = new ArrayCollection();
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
     * @return Skill
     */
    public function setId(?int $id): Skill
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
     * @return Skill
     */
    #[ORM\PrePersist]
    public function setCreatedAt(): Skill
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
     * @return Skill
     */
    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function setUpdatedAt(): Skill
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
     * @return Skill
     */
    public function setName(string $name): Skill
    {
        $this->name = $name;
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
     * @return Skill
     */
    public function addPercentage(Percentage $percentage): Skill
    {
        if (!$this->percentages->contains($percentage)) {
            $this->percentages->add($percentage);
        }

        return $this;
    }

    /**
     * @param Percentage $percentage
     * @return Skill
     */
    public function removePercentage(Percentage $percentage): Skill
    {
        $this->percentages->removeElement($percentage);
        return $this;
    }
}
