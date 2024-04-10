<?php

namespace App\Manager;

use App\Entity\Achievement;
use Doctrine\ORM\EntityManagerInterface;

class AchievementManager
{

    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    public function create(string $name): Achievement
    {
        $achievement = (new Achievement())->setName($name);
        $this->entityManager->persist($achievement);
        $this->entityManager->flush();

        return $achievement;
    }
}
