<?php

namespace App\Repository;

use App\Entity\Skill;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepositoryProxy;
use Doctrine\Common\Collections\AbstractLazyCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Order;
use Doctrine\Common\Collections\Selectable;
use Doctrine\Persistence\ManagerRegistry;

class SkillRepository extends ServiceEntityRepositoryProxy
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Skill::class);
    }

    public function findByName(string $name): AbstractLazyCollection&Selectable
    {
        $criteria = Criteria::create()
            ->andWhere(Criteria::expr()->contains('name', $name))
            ->orderBy(['name' => Order::Ascending]);

        return $this->matching($criteria);
    }
}
