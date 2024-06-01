<?php

namespace App\Repository;

use App\Collection\EntityCollection;
use App\Controller\Api\v1\Achievement\Input\ReadData;
use App\Controller\Api\v1\Achievement\Input\SortEnum;
use App\Entity\Achievement;
use App\Entity\Student;
use App\Exception\BadRequestException;
use App\Exception\EntityNotFoundException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepositoryProxy;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

class AchievementRepository extends ServiceEntityRepositoryProxy
{

    use LimitTrait;

    public function __construct(
        ManagerRegistry $registry,
        private readonly EntityManagerInterface $entityManager
    ) {
        parent::__construct($registry, Achievement::class);
    }

    /**
     * @throws EntityNotFoundException
     * @throws BadRequestException
     */
    public function getAchievements(ReadData $data): EntityCollection
    {
        $qb = $this->entityManager->createQueryBuilder();
        $qb->select('a')->from(Achievement::class, 'a');
        $this->setLimit($data, $qb);
        $this->setOffset($data, $qb);

        if ($data->name !== null) {
            $qb->andWhere($qb->expr()->like('a.name', ':name'))->setParameter('name', "$data->name%");
        }

        if ($data->studentId !== null) {
            $student = $this->entityManager->getRepository(Student::class)->find($data->studentId);
            if ($student === null) {
                throw new EntityNotFoundException('Student not found');
            }

            $qb->innerJoin('a.unlockedAchievements', 'ua_0')
                ->andWhere($qb->expr()->eq('ua_0.student', ':student'))
                ->setParameter('student', $data->studentId);
        }

        switch ($data->sort) {
            case SortEnum::RARITY:
                $qb->leftJoin('a.unlockedAchievements', 'ua_1')
                    ->groupBy('a.id')
                    ->addOrderBy('COUNT(ua_1.id)', 'ASC')
                    ->addOrderBy('a.name', 'ASC');
                break;
            case SortEnum::DEFAULT:
                $qb->orderBy('a.name', 'ASC');
        }

        return new EntityCollection($qb->getQuery()->getResult());
    }
}
