<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepositoryProxy;
use Doctrine\Persistence\ManagerRegistry;

class UserRepository extends ServiceEntityRepositoryProxy
{

    public function __construct(
        ManagerRegistry $registry
    ) {
        parent::__construct($registry, User::class);
    }
}
