<?php

namespace App\Repository;

use App\Entity\Student;
use App\Entity\UnlockedAchievement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepositoryProxy;
use Doctrine\Common\Collections\AbstractLazyCollection;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Selectable;
use Doctrine\Persistence\ManagerRegistry;

class StudentRepository extends ServiceEntityRepositoryProxy
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Student::class);
    }

    public function findByName(string $name): AbstractLazyCollection&Selectable
    {
        // todo CONCAT_WS(' ', first_name, last_name) like :name OR CONCAT_WS(' ', last_name, first_name) like :name
        $expression = Criteria::expr();
        $criteria = Criteria::create()->andWhere(
            $expression->orX(
                $expression->startsWith('first_name', $name),
                $expression->startsWith('last_name', $name),
            )
        );

        return $this->matching($criteria);
    }

    public function getAchievements(Student $student): ArrayCollection
    {
        $achievements = new ArrayCollection();

        /** @var UnlockedAchievement $unlockedAchievement */
        foreach ($student->getUnlockedAchievements() as $unlockedAchievement) {
            $achievements->add($unlockedAchievement->getAchievement());
        }

        return $achievements;
    }
}
