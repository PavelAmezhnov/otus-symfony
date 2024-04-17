<?php

namespace App\Manager;

use App\Entity\CompletedTask;
use App\Entity\Student;
use App\Entity\Task;
use Doctrine\ORM\EntityManagerInterface;

class CompletedTaskManager
{

    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    public function create(Student $student, Task $task): CompletedTask
    {
        $completedTask = (new CompletedTask())
            ->setStudent($student)
            ->setTask($task);
        $student->addCompletedTask($completedTask);
        $task->addCompletedTask($completedTask);
        $this->entityManager->persist($completedTask);
        $this->entityManager->flush();

        return $completedTask;
    }

    public function rate(CompletedTask $completedTask, int $grade): CompletedTask
    {
        $completedTask->setGrade($grade);
        $completedTask->setFinishedAt();
        $this->entityManager->flush();

        return $completedTask;
    }
}
