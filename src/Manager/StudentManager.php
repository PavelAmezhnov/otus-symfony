<?php

namespace App\Manager;

use App\Entity\Student;
use Doctrine\ORM\EntityManagerInterface;

class StudentManager
{

    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    public function create(string $firstName, string $lastname): Student
    {
        $student = (new Student())
            ->setFirstName($firstName)
            ->setLastName($lastname);
        $this->entityManager->persist($student);
        $this->entityManager->flush();

        return $student;
    }
}
