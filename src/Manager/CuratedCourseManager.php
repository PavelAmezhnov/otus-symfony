<?php

namespace App\Manager;

use App\Controller\Api\v1\CuratedCourse\Input\CreateData;
use App\Entity\Course;
use App\Entity\CuratedCourse;
use App\Entity\Teacher;
use App\Exception\BadRequestException;
use App\Exception\EntityNotFoundException;
use Doctrine\ORM\EntityManagerInterface;
use Throwable;

class CuratedCourseManager
{

    public function __construct(
        private readonly EntityManagerInterface $entityManager
    ) {

    }

    /**
     * @throws EntityNotFoundException
     * @throws BadRequestException
     */
    public function create(CreateData $dto): CuratedCourse
    {
        $teacher = $this->entityManager->getRepository(Teacher::class)->find($dto->teacherId);
        if ($teacher === null) {
            throw new EntityNotFoundException('Teacher not found');
        }

        $course = $this->entityManager->getRepository(Course::class)->find($dto->courseId);
        if ($course === null) {
            throw new EntityNotFoundException('Course not found');
        }

        $curatedCourse = (new CuratedCourse())
            ->setStudent($teacher)
            ->setCourse($course);
        $this->entityManager->persist($curatedCourse);

        try {
            $this->entityManager->flush();
        } catch (Throwable $e) {
            throw new BadRequestException($e->getMessage());
        }

        return $curatedCourse;
    }
}
