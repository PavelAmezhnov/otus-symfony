<?php

namespace App\Repository;

use App\Collection\EntityCollection;
use App\Controller\Api\v1\Subscription\Input\ReadData;
use App\Entity\Course;
use App\Entity\Student;
use App\Entity\Subscription;
use App\Exception\EntityNotFoundException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepositoryProxy;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

class SubscriptionRepository extends ServiceEntityRepositoryProxy
{
    public function __construct(
        ManagerRegistry $registry,
        private readonly EntityManagerInterface $entityManager
    ) {
        parent::__construct($registry, Subscription::class);
    }

    /**
     * @throws EntityNotFoundException
     */
    public function getSubscriptions(ReadData $dto): EntityCollection
    {
        $qb = $this->entityManager->createQueryBuilder();
        $qb->select('s')
            ->from(Subscription::class, 's')
            ->orderBy('s.id', 'ASC')
            ->setFirstResult($dto->perPage * ($dto->page - 1))
            ->setMaxResults($dto->perPage);

        if ($dto->studentId !== null) {
            $student = $this->entityManager->getRepository(Student::class)->find($dto->studentId);
            if ($student === null) {
                throw new EntityNotFoundException('Student not found');
            }

            $qb->andWhere($qb->expr()->eq('s.student', ':student'))->setParameter('student', $dto->studentId);
        }

        if ($dto->courseId !== null) {
            $course = $this->entityManager->getRepository(Course::class)->find($dto->courseId);
            if ($course === null) {
                throw new EntityNotFoundException('Course not found');
            }

            $qb->andWhere($qb->expr()->eq('s.course', ':course'))->setParameter('course', $dto->courseId);
        }

        return new EntityCollection($qb->getQuery()->getResult());
    }
}
