<?php

namespace App\Repository;

use App\Collection\EntityCollection;
use App\Controller\Api\v1\Student\Input\ReadData;
use App\Entity\Course;
use App\Entity\Student;
use App\Exception\BadRequestException;
use App\Exception\EntityNotFoundException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepositoryProxy;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

class StudentRepository extends ServiceEntityRepositoryProxy
{

    use LimitTrait;

    public function __construct(
        ManagerRegistry $registry,
        private readonly EntityManagerInterface $entityManager
    ) {
        parent::__construct($registry, Student::class);
    }

    /**
     * @throws EntityNotFoundException
     * @throws BadRequestException
     */
    public function getStudents(ReadData $data): EntityCollection
    {
        $qb = $this->entityManager->createQueryBuilder();
        $qb->select('s')
            ->from(Student::class, 's')
            ->orderBy('s.id', 'ASC');
        $this->setLimit($data, $qb);
        $this->setOffset($data, $qb);

        if ($data->name !== null) {
            $qb->andWhere(
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
            ->setParameter('name', "$data->name%");
        }

        if ($data->courseId !== null) {
            $course = $this->entityManager->getRepository(Course::class)->find($data->courseId);
            if ($course === null) {
                throw new EntityNotFoundException('Course not found');
            }

            $qb->innerJoin('s.subscriptions', 'subscription')
                ->andWhere($qb->expr()->eq('subscription.course', ':courseId'))
                ->setParameter('courseId', $data->courseId);
        }

        return new EntityCollection($qb->getQuery()->getResult());
    }

    /**
     * @throws EntityNotFoundException
     */
    public function getStudentById(int $id): Student
    {
        $student = $this->find($id);
        if ($student === null) {
            throw new EntityNotFoundException();
        }

        return $student;
    }
}
