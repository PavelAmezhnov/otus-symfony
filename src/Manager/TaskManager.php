<?php

namespace App\Manager;

use App\Entity\Lesson;
use App\Entity\Percentage;
use App\Entity\Task;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class TaskManager
{

    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    public function create(string $name, Lesson $lesson): Task
    {
        $task = (new Task())
            ->setName($name)
            ->setLesson($lesson);
        $lesson->addTask($task);
        $this->entityManager->persist($task);
        $this->entityManager->flush();

        return $task;
    }

    /**
     * @throws Exception
     */
    public function addPercentage(Task $task, Percentage $percentage): Task
    {
        $percents = 0;
        /** @var Percentage $p */
        foreach ($task->getPercentages() as $p) {
            $percents += $p->getPercent();
        }

        if ($percents + $percentage->getPercent() > 100) {
            throw new Exception();
        }

        $task->addPercentage($percentage);
        $percentage->setTask($task);
        $this->entityManager->flush();

        return $task;
    }

    public function changeLesson(Task $task, Lesson $lesson): Task
    {
        $task->getLesson()->removeTask($task);
        $task->removeLesson()->setLesson($lesson);
        $this->entityManager->flush();

        return $task;
    }
}
