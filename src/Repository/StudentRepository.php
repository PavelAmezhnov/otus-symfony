<?php

namespace App\Repository;

use App\Entity\Student;
use App\Entity\UnlockedAchievement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepositoryProxy;
use Doctrine\Common\Collections\AbstractLazyCollection;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Selectable;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

class StudentRepository extends ServiceEntityRepositoryProxy
{
    public function __construct(
        ManagerRegistry $registry,
        private readonly EntityManagerInterface $entityManager
    ) {
        parent::__construct($registry, Student::class);
    }

    public function findByName(string $name)
    {
        $qb = $this->entityManager->createQueryBuilder();
        $qb->select('s')
            ->from(Student::class, 's')
            ->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->like(
                        $qb->expr()->concat('s.firstName', $qb->expr()->literal(' '), 's.lastName'),
                        ':name'
                    ),
                    $qb->expr()->like(
                        $qb->expr()->concat('s.lastName', $qb->expr()->literal(' '), 's.firstName'),
                        ':name'
                    )
                )
            )
            ->setParameter('name', "$name%")
        ;

        return $qb->getQuery()->getResult();
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
