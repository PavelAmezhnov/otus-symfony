<?php

namespace App\Service;

use App\Collection\EntityCollection;
use App\Controller\Api\v1\CompletedTask\Input\ReadData;
use App\Entity\CompletedTask;
use App\Entity\Course;
use App\Entity\Lesson;
use App\Entity\Percentage;
use App\Entity\Skill;
use App\Entity\Student;
use App\Entity\Task;
use App\Exception\BadRequestException;
use App\Exception\EntityNotFoundException;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class CompletedTaskService
{

    public function __construct(
        private readonly EntityManagerInterface $entityManager
    ) {

    }

    /**
     * @throws EntityNotFoundException
     * @throws BadRequestException
     */
    public function read(ReadData $dto): int|float|EntityCollection
    {
        if ($dto->lessonId !== null) {
            return $this->getTotalGradeForLesson($dto);
        }

        if ($dto->skillId !== null) {
            return $this->getTotalGradeForSkill($dto);
        }

        if ($dto->courseId !== null) {
            return $this->getTotalGradeForCourse($dto);
        }

        if ($dto->finishedAtGTE !== null && $dto->finishedAtLTE !== null) {
            return $this->getTotalGradeInTimeRange($dto);
        }

        return $this->entityManager->getRepository(CompletedTask::class)->read($dto);
    }

    /**
     * Возвращает полученный студентом суммарный балл за все задания урока
     *
     * @throws BadRequestException
     * @throws EntityNotFoundException
     */
    private function getTotalGradeForLesson(ReadData $dto): int
    {
        if ($dto->lessonId === null) {
            throw new BadRequestException('lessonId required');
        }

        $lesson = $this->entityManager->getRepository(Lesson::class)->find($dto->lessonId);
        if ($lesson === null) {
            throw new EntityNotFoundException('Lesson not found');
        }

        if ($dto->studentId === null) {
            throw new BadRequestException('studentId required');
        }

        $student = $this->entityManager->getRepository(Student::class)->find($dto->studentId);
        if ($student === null) {
            throw new EntityNotFoundException('Student not found');
        }

        $criteria = Criteria::create()
            ->andWhere(Criteria::expr()->eq('student', $student))
            ->andWhere(Criteria::expr()->in(
                'task',
                array_map(static fn(Task $t) => $t->getId(), $lesson->getTasks()->toArray())
            ));
        $completedTasks = $this->entityManager->getRepository(CompletedTask::class)->matching($criteria);

        $totalGrade = 0;
        /** @var CompletedTask $completedTask */
        foreach ($completedTasks as $completedTask) {
            $totalGrade += $completedTask->getGrade();
        }

        return $totalGrade;
    }

    /**
     * Возвращает полученный студентом суммарный балл по всем выполненным заданиям, включающим указанный навыка
     *
     * @throws BadRequestException
     * @throws EntityNotFoundException
     */
    private function getTotalGradeForSkill(ReadData $dto): float
    {
        if ($dto->skillId === null) {
            throw new BadRequestException('skillId required');
        }

        $skill = $this->entityManager->getRepository(Skill::class)->find($dto->skillId);
        if ($skill === null) {
            throw new EntityNotFoundException('Skill not found');
        }

        if ($dto->studentId === null) {
            throw new BadRequestException('studentId required');
        }

        $student = $this->entityManager->getRepository(Student::class)->find($dto->studentId);
        if ($student === null) {
            throw new EntityNotFoundException('Student not found');
        }

        $completedTasks = $this->entityManager->getRepository(CompletedTask::class)->findBy(['student' => $student]);
        $totalGrade = 0;

        /** @var CompletedTask $completedTask */
        foreach ($completedTasks as $completedTask) {
            /** @var Percentage $percentage */
            foreach ($completedTask->getTask()->getPercentages() as $percentage) {
                if ($percentage->getSkill() === $skill) {
                    $totalGrade += 0.01 * $percentage->getPercent() * $completedTask->getGrade();
                }
            }
        }

        return $totalGrade;
    }

    /**
     * Возвращает полученный студентом суммарный балл по всем пополненным заданиям указанного курса
     *
     * @throws BadRequestException
     * @throws EntityNotFoundException
     */
    private function getTotalGradeForCourse(ReadData $dto): int
    {
        if ($dto->courseId === null) {
            throw new BadRequestException('courseId required');
        }

        $course = $this->entityManager->getRepository(Course::class)->find($dto->courseId);
        if ($course === null) {
            throw new EntityNotFoundException('Course not found');
        }

        if ($dto->studentId === null) {
            throw new BadRequestException('studentId required');
        }

        $student = $this->entityManager->getRepository(Student::class)->find($dto->studentId);
        if ($student === null) {
            throw new EntityNotFoundException('Student not found');
        }

        $completedTasks = $this->entityManager->getRepository(CompletedTask::class)->findBy(['student' => $student]);
        $totalGrade = 0;

        /** @var CompletedTask $completedTask */
        foreach ($completedTasks as $completedTask) {
            if ($completedTask->getTask()->getLesson()->getCourse() === $course) {
                $totalGrade += $completedTask->getGrade();
            }
        }

        return $totalGrade;
    }

    /**
     * Возвращает полученный студентом суммарный балл по всем пополненным заданиям за указанный интервал времени
     *
     * @throws BadRequestException
     * @throws EntityNotFoundException
     * @throws Exception
     */
    private function getTotalGradeInTimeRange(ReadData $dto): int
    {
        if ($dto->finishedAtGTE === null) {
            throw new BadRequestException('finishedAtGTE required');
        }

        if ($dto->finishedAtLTE === null) {
            throw new BadRequestException('finishedAtLTE required');
        }

        $student = $this->entityManager->getRepository(Student::class)->find($dto->studentId);
        if ($student === null) {
            throw new EntityNotFoundException('Student not found');
        }

        $criteria = Criteria::create()
            ->andWhere(Criteria::expr()->eq('student', $student))
            ->andWhere(Criteria::expr()->gte('finishedAt', $dto->finishedAtGTE))
            ->andWhere(Criteria::expr()->lte('finishedAt', $dto->finishedAtLTE));
        $completedTasks = $this->entityManager->getRepository(CompletedTask::class)->matching($criteria);
        $totalGrade = 0;

        /** @var CompletedTask $completedTask */
        foreach ($completedTasks as $completedTask) {
            $totalGrade += $completedTask->getGrade();
        }

        return $totalGrade;
    }

    /**
     * @throws EntityNotFoundException
     */
    public function readById(int $id): array
    {
        $completedTask = $this->entityManager->getRepository(CompletedTask::class)->find($id);
        if ($completedTask === null) {
            throw new EntityNotFoundException('Completed task not found');
        }

        return $completedTask->toArray();
    }
}
