<?php

namespace App\Repository;

use App\Collection\EntityCollection;
use App\Controller\Api\v1\Task\Input\ReadData;
use App\Entity\Lesson;
use App\Entity\Skill;
use App\Entity\Task;
use App\Exception\BadRequestException;
use App\Exception\EntityNotFoundException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepositoryProxy;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

class TaskRepository extends ServiceEntityRepositoryProxy
{

    use LimitTrait;

    public function __construct(
        ManagerRegistry $registry,
        private readonly EntityManagerInterface $entityManager
    ) {
        parent::__construct($registry, Task::class);
    }

    /**
     * @throws EntityNotFoundException
     * @throws BadRequestException
     */
    public function getTasks(ReadData $dto): EntityCollection
    {
        $qb = $this->entityManager->createQueryBuilder();
        $qb->select('t')
            ->from(Task::class, 't')
            ->orderBy('t.name', 'ASC');
        $this->setLimit($dto, $qb);
        $this->setOffset($dto, $qb);

        if ($dto->name !== null) {
            $qb->andWhere($qb->expr()->like('t.name', ':name'))->setParameter('name', "$dto->name%");
        }

        if ($dto->lessonId !== null) {
            $lesson = $this->entityManager->getRepository(Lesson::class)->find($dto->lessonId);
            if ($lesson === null) {
                throw new EntityNotFoundException('Lesson no found');
            }

            $qb->andWhere($qb->expr()->eq('t.lesson', ':lesson'))->setParameter('lesson', $dto->lessonId);
        }

        if ($dto->skillId !== null) {
            $skill = $this->entityManager->getRepository(Skill::class)->find($dto->skillId);
            if ($skill === null) {
                throw new EntityNotFoundException('Skill not found');
            }

            $qb->innerJoin('t.percentages', 'p')
                ->andWhere($qb->expr()->gt('p.percent', 0))
                ->andWhere($qb->expr()->eq('p.skill', ':skill'))
                ->setParameter('skill', $dto->skillId);
        }

        return new EntityCollection($qb->getQuery()->getResult());
    }
}
