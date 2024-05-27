<?php

namespace App\Repository;

use App\Collection\EntityCollection;
use App\Controller\Api\v1\Skill\Input\ReadData;
use App\Entity\Skill;
use App\Exception\BadRequestException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepositoryProxy;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

class SkillRepository extends ServiceEntityRepositoryProxy
{

    use LimitTrait;

    public function __construct(
        ManagerRegistry $registry,
        public readonly EntityManagerInterface $entityManager
    ) {
        parent::__construct($registry, Skill::class);
    }

    /**
     * @throws BadRequestException
     */
    public function getSkills(ReadData $dto): EntityCollection
    {
        $qb = $this->entityManager->createQueryBuilder();
        $qb->select('s')
            ->from(Skill::class, 's')
            ->orderBy('s.name', 'ASC');
        $this->setLimit($dto, $qb);
        $this->setOffset($dto, $qb);

        if ($dto->name !== null) {
            $qb->andWhere($qb->expr()->like('s.name', ':name'))->setParameter('name', "$dto->name%");
        }

        return new EntityCollection($qb->getQuery()->getResult());
    }
}
