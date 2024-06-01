<?php

namespace App\Manager;

use App\Controller\Api\v1\Skill\Input\CreateData;
use App\Controller\Api\v1\Skill\Input\UpdateData;
use App\Entity\Skill;
use App\Exception\BadRequestException;
use App\Exception\EntityNotFoundException;
use Doctrine\ORM\EntityManagerInterface;
use Throwable;

class SkillManager
{

    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    /**
     * @throws BadRequestException
     */
    public function create(CreateData $dto): Skill
    {
        $skill = (new Skill())->setName($dto->name);
        $this->entityManager->persist($skill);

        try {
            $this->entityManager->flush();
        } catch (Throwable $e) {
            throw new BadRequestException($e->getMessage());
        }

        return $skill;
    }

    /**
     * @throws EntityNotFoundException|BadRequestException
     */
    public function update(UpdateData $dto)
    {
        $skill = $this->entityManager->getRepository(Skill::class)->find($dto->id);
        if ($skill === null) {
            throw new EntityNotFoundException('Skill not found');
        }

        $skill->setname($dto->name);
        try {
            $this->entityManager->flush();
        } catch (Throwable $e) {
            throw new BadRequestException($e->getMessage());
        }

        return $skill;
    }

    /**
     * @throws EntityNotFoundException
     */
    public function delete(int $id)
    {
        $skill = $this->entityManager->getRepository(Skill::class)->find($id);
        if ($skill === null) {
            throw new EntityNotFoundException('Skill not found');
        }

        $this->entityManager->remove($skill);
        $this->entityManager->flush();

        return null;
    }
}
