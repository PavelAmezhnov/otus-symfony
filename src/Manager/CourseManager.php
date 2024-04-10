<?php

namespace App\Manager;

use App\Entity\Course;
use Doctrine\ORM\EntityManagerInterface;

class CourseManager
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    public function create(string $name): Course
    {
        $course = (new Course())->setName($name);
        $this->entityManager->persist($course);
        $this->entityManager->flush();

        return $course;
    }
}
