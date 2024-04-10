<?php

namespace App\Manager;

use App\Entity\Course;
use App\Entity\Student;
use App\Entity\Subscription;
use Doctrine\ORM\EntityManagerInterface;

class SubscriptionManager
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    public function subscribe(Student $student, Course $course): Subscription
    {
        $subscription = (new Subscription())
            ->setStudent($student)
            ->setCourse($course);
        $this->entityManager->persist($subscription);
        $this->entityManager->flush();

        return $subscription;
    }
}
