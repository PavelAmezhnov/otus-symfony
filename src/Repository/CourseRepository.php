<?php

namespace App\Repository;

use App\Collection\EntityCollection;
use App\Controller\Api\v1\Course\Input\ReadData;
use App\Entity\Course;
use App\Entity\Subscription;
use App\Exception\EntityNotFoundException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepositoryProxy;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

class CourseRepository extends ServiceEntityRepositoryProxy
{

    public function __construct(
        ManagerRegistry $registry,
        private readonly EntityManagerInterface $entityManager
    ) {
        parent::__construct($registry, Course::class);
    }

    public function read(ReadData $dto): EntityCollection
    {
        $qb = $this->entityManager->createQueryBuilder();
        $qb->select('c')
            ->from(Course::class, 'c')
            ->orderBy('c.id', 'ASC')
            ->setFirstResult($dto->perPage * ($dto->page - 1))
            ->setMaxResults($dto->perPage);

        if ($dto->name !== null) {
            $qb->andWhere($qb->expr()->like('c.name', ':name'))->setParameter('name', "$dto->name%");
        }

        return new EntityCollection($qb->getQuery()->getResult());
    }

    /**
     * @throws EntityNotFoundException
     */
    public function readById(int $id): array
    {
        $course = $this->find($id);
        if ($course === null) {
            throw new EntityNotFoundException('Course not found');
        }

        $result = $course->toArray();
        $result['students'] = array_map(
            static fn(Subscription $s) => $s->getStudent()->toArray(),
            $course->getSubscriptions()->toArray()
        );

        return $result;
    }
}
