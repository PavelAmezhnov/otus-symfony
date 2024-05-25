<?php

namespace App\Manager;

use App\Controller\Api\v1\Lesson\Input\CreateData;
use App\Controller\Api\v1\Lesson\Input\UpdateData;
use App\Entity\Course;
use App\Entity\Lesson;
use App\Exception\BadRequestException;
use App\Exception\EntityNotFoundException;
use App\Repository\LessonRepository;
use Doctrine\ORM\EntityManagerInterface;
use Throwable;

class LessonManager
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly LessonRepository $lessonRepository
    ) {
    }

    /**
     * @throws EntityNotFoundException
     * @throws BadRequestException
     */
    public function create(CreateData $dto): Lesson
    {
        $course = $this->entityManager->getRepository(Course::class)->find($dto->courseId);
        if ($course === null) {
            throw new EntityNotFoundException('Course not found');
        }

        $lesson = (new Lesson())
            ->setName($dto->name)
            ->setCourse($course);
        $course->addLesson($lesson);
        $this->entityManager->persist($lesson);

        try {
            $this->entityManager->flush();
        } catch (Throwable $e) {
            throw new BadRequestException($e->getMessage());
        }

        return $lesson;
    }

    /**
     * @throws EntityNotFoundException
     * @throws BadRequestException
     */
    public function update(UpdateData $dto): Lesson
    {
        $lesson = $this->lessonRepository->find($dto->id);
        if ($lesson === null) {
            throw new EntityNotFoundException('Lesson not found');
        }

        $lesson->setName($dto->name);
        if ($dto->courseId !== null) {
            $course = $this->entityManager->getRepository(Course::class)->find($dto->courseId);
            if ($course === null) {
                throw new EntityNotFoundException('Course not found');
            }

            $this->changeCourse($lesson, $course);
        }

        try {
            $this->entityManager->flush();
        } catch (Throwable $e) {
            throw new BadRequestException($e->getMessage());
        }

        return $lesson;
    }

    private function changeCourse(Lesson $lesson, Course $course): void
    {
        $lesson->getCourse()->removeLesson($lesson);
        $lesson->removeCourse()->setCourse($course);
    }

    /**
     * @throws EntityNotFoundException
     */
    public function delete(int $id)
    {
        $lesson = $this->lessonRepository->find($id);
        if ($lesson === null) {
            throw new EntityNotFoundException('Lesson not found');
        }

        $this->entityManager->remove($lesson);
        $this->entityManager->flush();

        return null;
    }
}
