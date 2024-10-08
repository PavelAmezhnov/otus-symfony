<?php

namespace App\Repository;

use App\Collection\EntityCollection;
use App\Controller\Api\v1\Percentage\Input\ReadData;
use App\Entity\Percentage;
use App\Entity\Task;
use App\Exception\BadRequestException;
use App\Exception\EntityNotFoundException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepositoryProxy;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

class PercentageRepository extends ServiceEntityRepositoryProxy
{

    use LimitTrait;

    public function __construct(
        ManagerRegistry $registry,
        private readonly EntityManagerInterface $entityManager
    ) {
        parent::__construct($registry, Percentage::class);
    }

    /**
     * @throws EntityNotFoundException
     * @throws BadRequestException
     */
    public function getPercentages(ReadData $dto): EntityCollection
    {
        $task = $this->entityManager->getRepository(Task::class)->find($dto->taskId);
        if ($task === null) {
            throw new EntityNotFoundException('Task not found');
        }

        $qb = $this->entityManager->createQueryBuilder();
        $qb->select('p')
            ->from(Percentage::class, 'p')
            ->andWhere($qb->expr()->eq('p.task', $dto->taskId))
            ->orderBy('p.percent', 'ASC');
        $this->setLimit($dto, $qb);
        $this->setOffset($dto, $qb);

        return new EntityCollection($qb->getQuery()->getResult());
    }
}
