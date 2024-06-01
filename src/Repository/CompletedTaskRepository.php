<?php

namespace App\Repository;

use App\Collection\EntityCollection;
use App\Controller\Api\v1\CompletedTask\Input\ReadData;
use App\Entity\CompletedTask;
use App\Entity\Student;
use App\Entity\Task;
use App\Exception\BadRequestException;
use App\Exception\EntityNotFoundException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepositoryProxy;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

class CompletedTaskRepository extends ServiceEntityRepositoryProxy
{

    use LimitTrait;

    public function __construct(
        ManagerRegistry $registry,
        private readonly EntityManagerInterface $entityManager
    ) {
        parent::__construct($registry, CompletedTask::class);
    }

    /**
     * @throws EntityNotFoundException
     * @throws BadRequestException
     */
    public function read(ReadData $dto): EntityCollection
    {
        $criteria = Criteria::create();
        $this->setLimit($dto, $criteria);
        $this->setOffset($dto, $criteria);

        if ($dto->studentId !== null) {
            $student = $this->entityManager->getRepository(Student::class)->find($dto->studentId);
            if ($student === null) {
                throw new EntityNotFoundException('Student not found');
            }

            $criteria->andWhere(Criteria::expr()->eq('student', $student));
        }

        if ($dto->taskId !== null) {
            $task = $this->entityManager->getRepository(Task::class)->find($dto->taskId);
            if ($task === null) {
                throw new EntityNotFoundException('Task not found');
            }

            $criteria->andWhere(Criteria::expr()->eq('task', $task));
        }

        return new EntityCollection(
            $this->entityManager->getRepository(CompletedTask::class)->matching($criteria)->toArray()
        );
    }
}
