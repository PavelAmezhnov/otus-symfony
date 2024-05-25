<?php

namespace App\Manager;

use App\Controller\Api\v1\Task\Input\CreateData;
use App\Controller\Api\v1\Task\Input\UpdateData;
use App\Entity\Lesson;
use App\Entity\Task;
use App\Exception\BadRequestException;
use App\Exception\EntityNotFoundException;
use Doctrine\ORM\EntityManagerInterface;
use Throwable;

class TaskManager
{

    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    /**
     * @throws EntityNotFoundException
     * @throws BadRequestException
     */
    public function create(CreateData $dto): Task
    {
        /** @var Lesson|null $lesson */
        $lesson = $this->entityManager->getRepository(Lesson::class)->find($dto->lessonId);
        if ($lesson === null) {
            throw new EntityNotFoundException('Lesson not found');
        }

        $task = (new Task())
            ->setName($dto->name)
            ->setLesson($lesson);
        $lesson->addTask($task);
        $this->entityManager->persist($task);

        try {
            $this->entityManager->flush();
        } catch (Throwable $e) {
            throw new BadRequestException($e->getMessage());
        }

        return $task;
    }

    /**
     * @throws EntityNotFoundException
     * @throws BadRequestException
     */
    public function update(UpdateData $dto): Task
    {
        /** @var Task|null $task */
        $task = $this->entityManager->getRepository(Task::class)->find($dto->id);
        if ($task === null) {
            throw new EntityNotFoundException('Task not found');
        }

        if ($dto->name !== null) {
            $task->setName($dto->name);
        }

        if ($dto->lessonId !== null) {
            /** @var Lesson|null $lesson */
            $lesson = $this->entityManager->getRepository(Lesson::class)->find($dto->lessonId);
            if ($lesson === null) {
                throw new EntityNotFoundException('Lesson not found');
            }

            $task->removeLesson()->setLesson($lesson);
        }

        try {
            $this->entityManager->flush();
        } catch (Throwable $e) {
            throw new BadRequestException($e->getMessage());
        }

        return $task;
    }

    /**
     * @throws EntityNotFoundException
     */
    public function delete(int $id)
    {
        /** @var Task|null $task */
        $task = $this->entityManager->getRepository(Task::class)->find($id);
        if ($task === null) {
            throw new EntityNotFoundException('Task not found');
        }

        $this->entityManager->remove($task);
        $this->entityManager->flush();

        return null;
    }
}
