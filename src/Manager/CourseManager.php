<?php

namespace App\Manager;

use App\Controller\Api\v1\Course\Input\CreateData;
use App\Controller\Api\v1\Course\Input\UpdateData;
use App\Entity\Course;
use App\Exception\BadRequestException;
use App\Exception\EntityNotFoundException;
use App\Repository\CourseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Throwable;

class CourseManager
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly CourseRepository $courseRepository
    ) {
    }

    /**
     * @throws BadRequestException
     */
    public function create(CreateData $dto): Course
    {
        $course = (new Course())->setName($dto->name);
        $this->entityManager->persist($course);
        try {
            $this->entityManager->flush();
        } catch (Throwable $e) {
            throw new BadRequestException($e->getMessage());
        }

        return $course;
    }

    /**
     * @throws EntityNotFoundException
     * @throws BadRequestException
     */
    public function update(UpdateData $dto): Course
    {
        $course = $this->courseRepository->find($dto->id);
        if ($course === null) {
            throw new EntityNotFoundException('Course not found');
        }

        $course->setName($dto->name);
        try {
            $this->entityManager->flush();
        } catch (Throwable $e) {
            throw new BadRequestException($e->getMessage());
        }

        return $course;
    }

    /**
     * @throws EntityNotFoundException
     */
    public function delete(int $id)
    {
        $course = $this->courseRepository->find($id);
        if ($course === null) {
            throw new EntityNotFoundException();
        }

        $this->entityManager->remove($course);
        $this->entityManager->flush();

        return null;
    }
}
