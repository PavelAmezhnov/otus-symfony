<?php

namespace App\Repository;

use App\Exception\BadRequestException;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\QueryBuilder;

trait LimitTrait
{

    /**
     * @throws BadRequestException
     */
    private function setLimit(object $dto, QueryBuilder|Criteria $qbc): void
    {
        if (!isset($dto->perPage)) {
            throw new BadRequestException("'perPage' parameter must be specified");
        }

        $qbc->setMaxResults($dto->perPage);
    }

    /**
     * @throws BadRequestException
     */
    private function setOffset(object $dto, QueryBuilder|Criteria $qbc): void
    {
        if (!isset($dto->perPage)) {
            throw new BadRequestException("'perPage' parameter must be specified");
        }

        if (!isset($dto->page)) {
            throw new BadRequestException("'page' parameter must be specified");
        }

        $qbc->setFirstResult($dto->perPage * ($dto->page - 1));
    }
}
