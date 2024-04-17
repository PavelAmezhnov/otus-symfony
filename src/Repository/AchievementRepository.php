<?php

namespace App\Repository;

use App\Entity\Achievement;
use App\Entity\Student;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepositoryProxy;
use Doctrine\Persistence\ManagerRegistry;

class AchievementRepository extends ServiceEntityRepositoryProxy
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Achievement::class);
    }

    public function getSortedByRarityAchievementList(): array
    {
        $achievements = $this->findAll();
        usort(
            $achievements,
            static fn(Achievement $a, Achievement $b) =>
                $a->getUnlockedAchievements()->count() <=> $b->getUnlockedAchievements()->count()
        );

        return $achievements;
    }

    public function getCountStudentsWithAchievementInPercentage(Achievement $achievement): float
    {
        $countUnlockedAchievements = $achievement->getUnlockedAchievements()->count();
        $countStudents = count($this->getEntityManager()->getRepository(Student::class)->findAll());

        return $countStudents === 0
            ? 0.0
            : round(100 * ($countUnlockedAchievements / $countStudents), 1, PHP_ROUND_HALF_DOWN);
    }
}
