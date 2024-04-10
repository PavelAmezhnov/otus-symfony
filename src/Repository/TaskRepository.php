<?php

namespace App\Repository;

use App\Entity\Percentage;
use App\Entity\Skill;
use App\Entity\Task;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepositoryProxy;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Persistence\ManagerRegistry;

class TaskRepository extends ServiceEntityRepositoryProxy
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Task::class);
    }

    public function getTasksWithSpecificSkill(Skill $skill): ArrayCollection
    {
        $tasks = new ArrayCollection();

        /** @var Task $task */
        foreach ($this->findAll() as $task) {
            /** @var Percentage $percentage */
            foreach ($task->getPercentages() as $percentage) {
                if ($percentage->getSkill() === $skill && $percentage->getPercent() > 0) {
                    $tasks->add($task);
                    break;
                }
            }
        }

        return $tasks;
    }
}
