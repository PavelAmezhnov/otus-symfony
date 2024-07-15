<?php

namespace App\Service;

use App\Collection\EntityCollection;
use App\Controller\Api\v1\CompletedTask\Input\CreateData;
use App\Controller\Api\v1\CompletedTask\Input\ReadData;
use App\Controller\Api\v1\CompletedTask\Input\UpdateData;
use App\Entity\CompletedTask;
use App\Entity\Course;
use App\Entity\CuratedCourse;
use App\Entity\Lesson;
use App\Entity\Percentage;
use App\Entity\Skill;
use App\Entity\Student;
use App\Entity\Task;
use App\Entity\Teacher;
use App\Exception\AccessDeniedException;
use App\Exception\BadRequestException;
use App\Exception\EntityNotFoundException;
use App\Manager\CompletedTaskManager;
use App\Manager\StaffManager;
use App\Manager\StudentManager;
use App\Manager\TeacherManager;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\Security\Core\User\UserInterface;

class CompletedTaskService
{

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly CompletedTaskManager $completedTaskManager
    ) {

    }

    /**
     * @throws EntityNotFoundException
     * @throws BadRequestException
     * @throws AccessDeniedException
     */
    public function create(CreateData $dto, UserInterface $user): CompletedTask
    {
        /** @var Student|null $student */
        $student = $this->entityManager->getRepository(Student::class)->find($dto->studentId);
        if ($student === null) {
            throw new EntityNotFoundException('Student not found');
        }

        if ($student->getUser() !== $user) {
            throw new AccessDeniedException("You can't do this action");
        }

        return $this->completedTaskManager->create($dto);
    }


    /**
     * @throws EntityNotFoundException
     * @throws AccessDeniedException
     */
    public function update(UpdateData $dto, UserInterface $user): CompletedTask
    {
        /** @var CompletedTask|null $completedTask */
        $completedTask = $this->entityManager->getRepository(Student::class)->find($dto->id);
        if ($completedTask === null) {
            throw new EntityNotFoundException('Completed task not found');
        }

        $task = $completedTask->getTask();
        $lesson = $task->getLesson();
        $course = $lesson->getCourse();

        /** @var CuratedCourse $curatedCourse */
        foreach ($course->getCuratedCourses()->toArray() as $curatedCourse) {
            if ($curatedCourse->getTeacher() === $user) {
                return $this->completedTaskManager->update($dto);
            }
        }

        throw new AccessDeniedException("You can't do this action");
    }

    /**
     * @throws EntityNotFoundException
     * @throws BadRequestException
     * @throws AccessDeniedException
     */
    public function read(ReadData $dto, UserInterface $user): int|float|EntityCollection
    {
        if ($dto->lessonId !== null) {
            return $this->getTotalGradeForLesson($dto, $user);
        }

        if ($dto->skillId !== null) {
            return $this->getTotalGradeForSkill($dto, $user);
        }

        if ($dto->courseId !== null) {
            return $this->getTotalGradeForCourse($dto, $user);
        }

        if ($dto->finishedAtGTE !== null && $dto->finishedAtLTE !== null) {
            return $this->getTotalGradeInTimeRange($dto, $user);
        }

        return $this->entityManager->getRepository(CompletedTask::class)->read($dto);
    }

    /**
     * Возвращает полученный студентом суммарный балл за все задания урока
     *
     * @throws BadRequestException
     * @throws EntityNotFoundException
     * @throws AccessDeniedException
     */
    private function getTotalGradeForLesson(ReadData $dto, UserInterface $user): int
    {
        if ($dto->lessonId === null) {
            throw new BadRequestException('lessonId required');
        }

        /** @var Lesson|null $lesson */
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

        // вычисление по текущему пользователю, можно ли ему выполнить это действие
        if ($user->hasRole(StudentManager::ROLE_STUDENT)) {
            if ($student->getUser() !== $user) {
                throw new AccessDeniedException("You can't do this action");
            }
        }

        if ($user->hasRole(TeacherManager::ROLE_TEACHER)) {
            $teacher = $this->entityManager->getRepository(Teacher::class)->findOneBy([['user' => $user]]);

            $course = $lesson->getCourse();
            $flag = true;
            /** @var CuratedCourse $curatedCourse */
            foreach ($course->getCuratedCourses()->toArray() as $curatedCourse) {
                if ($curatedCourse->getTeacher() === $teacher) {
                    $flag = false;
                }
            }

            if ($flag) {
                throw new AccessDeniedException("You can't do this action");
            }
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
     * @throws AccessDeniedException
     */
    private function getTotalGradeForSkill(ReadData $dto, UserInterface $user): float
    {
        if (array_intersect([StaffManager::ROLE_STAFF, StudentManager::ROLE_STUDENT], $user->getRoles())) {
            throw new AccessDeniedException("You can't do this action");
        }

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

        if ($user->hasRole(StudentManager::ROLE_STUDENT)) {
            if ($student->getUser() !== $user) {
                throw new AccessDeniedException("You can't do this action");
            }
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
     * @throws AccessDeniedException
     */
    private function getTotalGradeForCourse(ReadData $dto, UserInterface $user): int
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

        if ($user->hasRole(StudentManager::ROLE_STUDENT)) {
            if ($student->getUser() !== $user) {
                throw new AccessDeniedException("You can't do this action");
            }
        }

        if ($user->hasRole(TeacherManager::ROLE_TEACHER)) {
            $teacher = $this->entityManager->getRepository(Teacher::class)->findOneBy(['user' => $user]);

            $flag = true;
            /** @var CuratedCourse $curatedCourse */
            foreach ($course->getCuratedCourses()->toArray() as $curatedCourse) {
                if ($curatedCourse->getTeacher() === $teacher) {
                    $flag = false;
                }
            }

            if ($flag) {
                throw new AccessDeniedException("You can't do this action");
            }
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
    private function getTotalGradeInTimeRange(ReadData $dto, UserInterface $user): int
    {
        if (array_intersect([StaffManager::ROLE_STAFF, StudentManager::ROLE_STUDENT], $user->getRoles())) {
            throw new AccessDeniedException("You can't do this action");
        }

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

        if ($user->hasRole(StudentManager::ROLE_STUDENT)) {
            if ($student->getUser() !== $user) {
                throw new AccessDeniedException("You can't do this action");
            }
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
     * @throws AccessDeniedException
     */
    public function readById(int $id, UserInterface $user): array
    {
        $completedTask = $this->entityManager->getRepository(CompletedTask::class)->find($id);
        if ($completedTask === null) {
            throw new EntityNotFoundException('Completed task not found');
        }

        $student = $completedTask->getStudent();
        if ($user->hasRole(StudentManager::ROLE_STUDENT)) {
            if ($student->getUser() !== $user) {
                throw new AccessDeniedException("You can't do this action");
            }
        }

        if ($user->hasRole(TeacherManager::ROLE_TEACHER)) {
            $teacher = $this->entityManager->getRepository(Teacher::class)->findOneBy([['user' => $user]]);

            $task = $completedTask->getTask();
            $lesson = $task->getLesson();
            $course = $lesson->getCourse();
            $flag = true;

            /** @var CuratedCourse $curatedCourse */
            foreach ($course->getCuratedCourses()->toArray() as $curatedCourse) {
                if ($curatedCourse->getTeacher() === $teacher) {
                    $flag = false;
                }
            }

            if ($flag) {
                throw new AccessDeniedException("You can't do this action");
            }
        }

        return $completedTask->toArray();
    }
}
