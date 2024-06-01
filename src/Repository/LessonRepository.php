<?php

namespace App\Repository;

use App\Collection\EntityCollection;
use App\Controller\Api\v1\Lesson\Input\ReadData;
use App\Entity\Lesson;
use App\Exception\BadRequestException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepositoryProxy;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

class LessonRepository extends ServiceEntityRepositoryProxy
{

    use LimitTrait;

    public function __construct(
        ManagerRegistry $registry,
        private readonly EntityManagerInterface $entityManager
    ) {
        parent::__construct($registry, Lesson::class);
    }

    /**
     * @throws BadRequestException
     */
    public function getLessons(ReadData $dto): EntityCollection
    {
        $qb = $this->entityManager->createQueryBuilder();
        $qb->select('l')
            ->from(Lesson::class, 'l')
            ->orderBy('l.id', 'ASC');
        $this->setLimit($dto, $qb);
        $this->setOffset($dto, $qb);

        if ($dto->name !== null) {
            $qb->andWhere($qb->expr()->like('l.name', ':name'))->setParameter('name', "$dto->name%");
        }

        return new EntityCollection($qb->getQuery()->getResult());
    }
}
