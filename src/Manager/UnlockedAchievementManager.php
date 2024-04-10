<?php

namespace App\Manager;

use App\Entity\Achievement;
use App\Entity\Student;
use App\Entity\UnlockedAchievement;
use Doctrine\ORM\EntityManagerInterface;

class UnlockedAchievementManager
{

    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    public function give(Student $student, Achievement $achievement): UnlockedAchievement
    {
        $unlockedAchievement = (new UnlockedAchievement())
            ->setStudent($student)
            ->setAchievement($achievement);
        $student->addUnlockedAchievement($unlockedAchievement);
        $achievement->addUnlockedAchievement($unlockedAchievement);
        $this->entityManager->persist($unlockedAchievement);
        $this->entityManager->flush();

        return $unlockedAchievement;
    }
}
