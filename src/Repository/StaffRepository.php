<?php

namespace App\Repository;


use App\Entity\Staff;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepositoryProxy;
use Doctrine\Persistence\ManagerRegistry;

class StaffRepository extends ServiceEntityRepositoryProxy
{

    public function __construct(
        ManagerRegistry $registry
    ) {
        parent::__construct($registry, Staff::class);
    }
}
