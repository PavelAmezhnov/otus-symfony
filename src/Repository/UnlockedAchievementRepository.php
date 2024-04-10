<?php

namespace App\Repository;

use App\Entity\UnlockedAchievement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepositoryProxy;
use Doctrine\Persistence\ManagerRegistry;

class UnlockedAchievementRepository extends ServiceEntityRepositoryProxy
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UnlockedAchievement::class);
    }
}
