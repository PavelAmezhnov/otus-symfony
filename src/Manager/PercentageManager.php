<?php

namespace App\Manager;

use App\Entity\Percentage;
use App\Entity\Skill;
use App\Entity\Task;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class PercentageManager
{

    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    /**
     * @throws Exception
     */
    public function create(Task $task, Skill $skill, float $percent): Percentage
    {
        $percentage = (new Percentage())
            ->setTask($task)
            ->setSkill($skill)
            ->setPercent($percent);
        $task->addPercentage($percentage);
        $skill->addPercentage($percentage);
        $this->entityManager->persist($percentage);
        $this->entityManager->flush();

        return $percentage;
    }
}
