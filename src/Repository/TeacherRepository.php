<?php

namespace App\Repository;

use App\Entity\Teacher;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepositoryProxy;
use Doctrine\Persistence\ManagerRegistry;

class TeacherRepository extends ServiceEntityRepositoryProxy
{

    public function __construct(
        ManagerRegistry $registry
    ) {
        parent::__construct($registry, Teacher::class);
    }
}
