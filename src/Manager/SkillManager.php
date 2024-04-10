<?php

namespace App\Manager;

use App\Entity\Skill;
use Doctrine\ORM\EntityManagerInterface;

class SkillManager
{

    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    public function create(string $name): Skill
    {
        $skill = (new Skill())->setName($name);
        $this->entityManager->persist($skill);
        $this->entityManager->flush();

        return $skill;
    }
}
