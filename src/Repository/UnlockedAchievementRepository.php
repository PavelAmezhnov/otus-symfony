<?php

namespace App\Repository;

use App\Collection\EntityCollection;
use App\Controller\Api\v1\UnlockedAchievement\Input\ReadData;
use App\Entity\Achievement;
use App\Entity\Student;
use App\Entity\UnlockedAchievement;
use App\Exception\EntityNotFoundException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepositoryProxy;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

class UnlockedAchievementRepository extends ServiceEntityRepositoryProxy
{
    public function __construct(
        ManagerRegistry $registry,
        private readonly EntityManagerInterface $entityManager
    ) {
        parent::__construct($registry, UnlockedAchievement::class);
    }

    /**
     * @throws EntityNotFoundException
     */
    public function read(ReadData $dto): EntityCollection
    {
        $qb = $this->entityManager->createQueryBuilder();
        $qb->select('ua')
            ->from(UnlockedAchievement::class, 'ua')
            ->innerJoin('ua.student', 's')
            ->innerJoin('ua.achievement', 'a')
            ->orderBy('ua.id', 'ASC')
            ->setFirstResult($dto->perPage * ($dto->page - 1))
            ->setMaxResults($dto->perPage);

        if ($dto->studentId !== null) {
            $student = $this->entityManager->getRepository(Student::class)->find($dto->studentId);
            if ($student === null) {
                throw new EntityNotFoundException('Student not found');
            }

            $qb->andWhere($qb->expr()->eq('ua.student', ':student'))->setParameter('student', $dto->studentId);
        }

        if ($dto->achievementId !== null) {
            $achievement = $this->entityManager->getRepository(Achievement::class)->find($dto->achievementId);
            if ($achievement === null) {
                throw new EntityNotFoundException('Achievement not found');
            }

            $qb->andWhere($qb->expr()->eq('ua.achievement', ':achievement'))
                ->setParameter('achievement', $dto->achievementId);
        }

        return new EntityCollection($qb->getQuery()->getResult());
    }
}
