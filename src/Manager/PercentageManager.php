<?php

namespace App\Manager;

use App\Controller\Api\v1\Percentage\Input\CreateData;
use App\Controller\Api\v1\Percentage\Input\UpdateData;
use App\Entity\Percentage;
use App\Entity\Skill;
use App\Entity\Task;
use App\Exception\BadRequestException;
use App\Exception\EntityNotFoundException;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Throwable;

class PercentageManager
{

    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    /**
     * @throws Exception
     */
    public function create(CreateData $dto): Percentage
    {
        /** @var Task|null $task */
        $task = $this->entityManager->getRepository(Task::class)->find($dto->taskId);
        if ($task === null) {
            throw new EntityNotFoundException('Task not found');
        }

        /** @var Skill|null $skill */
        $skill = $this->entityManager->getRepository(Skill::class)->find($dto->skillId);
        if ($skill === null) {
            throw new EntityNotFoundException('Skill not found');
        }

        try {
            $percentage = (new Percentage())
                ->setTask($task)
                ->setSkill($skill)
                ->setPercent($dto->percent);

            $task->addPercentage($percentage);
        } catch (Throwable $e) {
            throw new BadRequestException($e->getMessage());
        }
        $skill->addPercentage($percentage);
        $this->entityManager->persist($percentage);

        try {
            $this->entityManager->flush();
        } catch (Throwable $e) {
            throw new BadRequestException($e->getMessage());
        }

        return $percentage;
    }

    /**
     * @throws EntityNotFoundException
     * @throws BadRequestException
     */
    public function update(UpdateData $dto): Percentage
    {
        /** @var Percentage|null $percentage */
        $percentage = $this->entityManager->getRepository(Percentage::class)->find($dto->id);
        if ($percentage === null) {
            throw new EntityNotFoundException('Percentage not found');
        }

        try {
            $percentage->setPercent($dto->percent);
        } catch (Throwable $e) {
            throw new BadRequestException($e->getMessage());
        }

        $this->entityManager->flush();

        return $percentage;
    }

    /**
     * @throws EntityNotFoundException
     */
    public function delete(int $id)
    {
        $percentage = $this->entityManager->getRepository(Percentage::class)->find($id);
        if ($percentage === null) {
            throw new EntityNotFoundException('Percentage not found');
        }

        $this->entityManager->remove($percentage);
        $this->entityManager->flush();

        return null;
    }
}
