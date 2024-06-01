<?php

namespace App\Manager;

use App\Controller\Api\v1\CompletedTask\Input\CreateData;
use App\Controller\Api\v1\CompletedTask\Input\UpdateData;
use App\Entity\CompletedTask;
use App\Entity\Student;
use App\Entity\Task;
use App\Exception\BadRequestException;
use App\Exception\EntityNotFoundException;
use App\Repository\CompletedTaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Throwable;

class CompletedTaskManager
{

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly CompletedTaskRepository $completedTaskRepository
    ) {
    }

    /**
     * @throws EntityNotFoundException
     * @throws BadRequestException
     */
    public function create(CreateData $dto): CompletedTask
    {
        $student = $this->entityManager->getRepository(Student::class)->find($dto->studentId);
        if ($student === null) {
            throw new EntityNotFoundException('Student not found');
        }

        $task = $this->entityManager->getRepository(Task::class)->find($dto->taskId);
        if ($task === null) {
            throw new EntityNotFoundException('Task not found');
        }

        $completedTask = (new CompletedTask())
            ->setStudent($student)
            ->setTask($task);
        $student->addCompletedTask($completedTask);
        $task->addCompletedTask($completedTask);

        if ($dto->grade !== null) {
            $this->rate($completedTask, $dto->grade);
        }

        $this->entityManager->persist($completedTask);
        try {
            $this->entityManager->flush();
        } catch (Throwable $e) {
            throw new BadRequestException($e->getMessage());
        }

        return $completedTask;
    }

    /**
     * @throws EntityNotFoundException
     */
    public function update(UpdateData $dto): CompletedTask
    {
        $completedTask = $this->completedTaskRepository->find($dto->id);
        if ($completedTask === null) {
            throw new EntityNotFoundException('Completed task not found');
        }

        $this->rate($completedTask, $dto->grade);
        $this->entityManager->flush();

        return $completedTask;
    }

    /**
     * @throws EntityNotFoundException
     */
    public function delete(int $id)
    {
        $completedTask = $this->completedTaskRepository->find($id);
        if ($completedTask === null) {
            throw new EntityNotFoundException('Completed task not found');
        }

        $this->entityManager->remove($completedTask);
        $this->entityManager->flush();

        return null;
    }

    private function rate(CompletedTask $completedTask, int $grade): void
    {
        $completedTask->setGrade($grade);
        $completedTask->setFinishedAt();
    }
}
