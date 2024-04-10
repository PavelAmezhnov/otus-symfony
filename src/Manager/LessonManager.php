<?php

namespace App\Manager;

use App\Entity\Course;
use App\Entity\Lesson;
use Doctrine\ORM\EntityManagerInterface;

class LessonManager
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    public function create(string $name, Course $course): Lesson
    {
        $lesson = (new Lesson())
            ->setName($name)
            ->setCourse($course);
        $course->addLesson($lesson);
        $this->entityManager->persist($lesson);
        $this->entityManager->flush();

        return $lesson;
    }

    public function changeCourse(Lesson $lesson, Course $course): Lesson
    {
        $lesson->getCourse()->removeLesson($lesson);
        $lesson->removeCourse()->setCourse($course);
        $this->entityManager->flush();

        return $lesson;
    }
}
