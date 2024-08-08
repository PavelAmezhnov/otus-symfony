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
use DateTime;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Cache\CacheException;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\Cache\CacheItem;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

class CompletedTaskService
{

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly CompletedTaskManager $completedTaskManager,
        private readonly TagAwareCacheInterface $cache,
        private readonly AsyncService $asyncService
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
    public function update(UpdateData $dto, UserInterface $user = null): ?CompletedTask
    {
        /** @var CompletedTask|null $completedTask */
        $completedTask = $this->entityManager->getRepository(CompletedTask::class)->find($dto->id);
        if ($completedTask === null) {
            throw new EntityNotFoundException('Completed task not found');
        }

        if ($user instanceof UserInterface) {
            $task = $completedTask->getTask();
            $lesson = $task->getLesson();
            $course = $lesson->getCourse();

            /** @var CuratedCourse $curatedCourse */
            foreach ($course->getCuratedCourses()->toArray() as $curatedCourse) {
                if ($curatedCourse->getTeacher() === $user) {
                    $this->asyncService->publishToExchange(
                        AsyncService::UPDATE_COMPLETED_TASK,
                        json_encode((array) $dto)
                    );
                    $this->asyncService->publishToExchange(
                        AsyncService::INVALIDATE_CACHE,
                        json_encode(['tags' => [(string) $completedTask->getStudent()->getId()]])
                    );

                    return null;
                }
            }

            throw new AccessDeniedException("You can't do this action");
        }

        return $this->completedTaskManager->update($dto);
    }

    /**
     * @throws EntityNotFoundException
     * @throws BadRequestException
     * @throws AccessDeniedException
     * @throws InvalidArgumentException
     * @throws CacheException
     */
    public function read(ReadData $dto, UserInterface $user = null): null|int|float|array
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

        return $this->getCompletedTasks($dto, $user);
    }

    /**
     * Возвращает полученный студентом суммарный балл за все задания урока
     *
     * @throws BadRequestException
     * @throws EntityNotFoundException
     * @throws AccessDeniedException
     * @throws InvalidArgumentException
     * @throws CacheException
     */
    private function getTotalGradeForLesson(ReadData $dto, UserInterface $user = null): null|int
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

        if ($user instanceof UserInterface) {
            if ($user->hasRole(StudentManager::ROLE_STUDENT)) {
                if ($student->getUser() !== $user) {
                    throw new AccessDeniedException("You can't do this action");
                }
            }

            if ($user->hasRole(TeacherManager::ROLE_TEACHER)) {
                $teacher = $this->entityManager->getRepository(Teacher::class)->findOneBy(['user' => $user->getId()]);

                $course = $lesson->getCourse();
                $flag = true;
                /** @var CuratedCourse $curatedCourse */
                foreach ($course->getCuratedCourses()->toArray() as $curatedCourse) {
                    if ($curatedCourse->getTeacher() === $teacher) {
                        $flag = false;
                        break;
                    }
                }

                if ($flag) {
                    throw new AccessDeniedException("You can't do this action");
                }
            }

            /** @var CacheItem $cacheItem */
            $cacheItem = $this->cache
                ->getItem(sprintf('completed_task__total_grade_for_lesson__%s_%s', $dto->lessonId, $dto->studentId));
            if ($cacheItem->isHit()) {
                return $cacheItem->get();
            }

            $this->asyncService->publishToExchange(
                AsyncService::READ_COMPLETED_TASKS,
                json_encode((array) $dto)
            );

            return null;
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

        /** @var CacheItem $cacheItem */
        $cacheItem = $this->cache
            ->getItem(sprintf('completed_task__total_grade_for_lesson__%s_%s', $dto->lessonId, $dto->studentId));
        $cacheItem->set($totalGrade);
        $cacheItem->tag((string) $dto->studentId);
        $this->cache->save($cacheItem);

        return $totalGrade;
    }

    /**
     * Возвращает полученный студентом суммарный балл по всем выполненным заданиям, включающим указанный навыка
     *
     * @throws BadRequestException
     * @throws EntityNotFoundException
     * @throws AccessDeniedException
     * @throws InvalidArgumentException
     * @throws CacheException
     */
    private function getTotalGradeForSkill(ReadData $dto, UserInterface $user = null): ?float
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

        if ($user instanceof UserInterface) {
            if (array_intersect([StaffManager::ROLE_STAFF, StudentManager::ROLE_STUDENT], $user->getRoles())) {
                throw new AccessDeniedException("You can't do this action");
            }

            if ($user->hasRole(StudentManager::ROLE_STUDENT)) {
                if ($student->getUser() !== $user) {
                    throw new AccessDeniedException("You can't do this action");
                }
            }

            /** @var CacheItem $cacheItem */
            $cacheItem = $this->cache
                ->getItem(sprintf('completed_task__total_grade_for_skill__%s_%s', $dto->skillId, $dto->studentId));
            if ($cacheItem->isHit()) {
                return $cacheItem->get();
            }

            $this->asyncService->publishToExchange(
                AsyncService::READ_COMPLETED_TASKS,
                json_encode((array) $dto)
            );

            return null;
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

        /** @var CacheItem $cacheItem */
        $cacheItem = $this->cache
            ->getItem(sprintf('completed_task__total_grade_for_skill__%s_%s', $dto->skillId, $dto->studentId));
        $cacheItem->set($totalGrade);
        $cacheItem->tag((string) $dto->studentId);
        $this->cache->save($cacheItem);

        return $totalGrade;
    }

    /**
     * Возвращает полученный студентом суммарный балл по всем выполненным заданиям указанного курса
     *
     * @throws BadRequestException
     * @throws EntityNotFoundException
     * @throws AccessDeniedException
     * @throws InvalidArgumentException
     * @throws CacheException
     */
    private function getTotalGradeForCourse(ReadData $dto, UserInterface $user = null): ?int
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

        if ($user instanceof UserInterface) {
            if ($user->hasRole(StudentManager::ROLE_STUDENT)) {
                if ($student->getUser() !== $user) {
                    throw new AccessDeniedException("You can't do this action");
                }
            }

            if ($user->hasRole(TeacherManager::ROLE_TEACHER)) {
                $teacher = $this->entityManager->getRepository(Teacher::class)->findOneBy(['user' => $user->getId()]);

                $flag = true;
                /** @var CuratedCourse $curatedCourse */
                foreach ($course->getCuratedCourses()->toArray() as $curatedCourse) {
                    if ($curatedCourse->getTeacher() === $teacher) {
                        $flag = false;
                        break;
                    }
                }

                if ($flag) {
                    throw new AccessDeniedException("You can't do this action");
                }
            }

            /** @var CacheItem $cacheItem */
            $cacheItem = $this->cache
                ->getItem(sprintf('completed_task__total_grade_for_course__%s_%s', $dto->courseId, $dto->studentId));
            if ($cacheItem->isHit()) {
                return $cacheItem->get();
            }

            $this->asyncService->publishToExchange(
                AsyncService::READ_COMPLETED_TASKS,
                json_encode((array) $dto)
            );

            return null;
        }

        $completedTasks = $this->entityManager->getRepository(CompletedTask::class)->findBy(['student' => $student]);
        $totalGrade = 0;

        /** @var CompletedTask $completedTask */
        foreach ($completedTasks as $completedTask) {
            if ($completedTask->getTask()->getLesson()->getCourse() === $course) {
                $totalGrade += $completedTask->getGrade();
            }
        }

        /** @var CacheItem $cacheItem */
        $cacheItem = $this->cache
            ->getItem(sprintf('completed_task__total_grade_for_course__%s_%s', $dto->courseId, $dto->studentId));
        $cacheItem->set($totalGrade);
        $cacheItem->tag((string) $dto->studentId);
        $this->cache->save($cacheItem);

        return $totalGrade;
    }

    /**
     * Возвращает полученный студентом суммарный балл по всем пополненным заданиям за указанный интервал времени
     *
     * @throws AccessDeniedException
     * @throws BadRequestException
     * @throws CacheException
     * @throws EntityNotFoundException
     * @throws InvalidArgumentException
     */
    private function getTotalGradeInTimeRange(ReadData $dto, UserInterface $user = null): ?int
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

        if ($user instanceof UserInterface) {
            if (array_intersect([StaffManager::ROLE_STAFF, StudentManager::ROLE_STUDENT], $user->getRoles())) {
                throw new AccessDeniedException("You can't do this action");
            }

            if ($user->hasRole(StudentManager::ROLE_STUDENT)) {
                if ($student->getUser() !== $user) {
                    throw new AccessDeniedException("You can't do this action");
                }
            }

            /** @var CacheItem $cacheItem */
            $cacheItem = $this->cache->getItem(sprintf(
                'completed_task__total_grade_in_time_range__%s_%s_%s',
                $dto->finishedAtGTE,
                $dto->finishedAtLTE,
                $dto->studentId
            ));
            if ($cacheItem->isHit()) {
                return $cacheItem->get();
            }

            $this->asyncService->publishToExchange(
                AsyncService::READ_COMPLETED_TASKS,
                json_encode((array) $dto)
            );

            return null;
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

        /** @var CacheItem $cacheItem */
        $cacheItem = $this->cache->getItem(sprintf(
            'completed_task__total_grade_in_time_range__%s_%s_%s',
            $dto->finishedAtGTE,
            $dto->finishedAtLTE,
            $dto->studentId
        ));
        $cacheItem->set($totalGrade);
        $cacheItem->tag((string) $dto->studentId);
        $this->cache->save($cacheItem);

        return $totalGrade;
    }

    /**
     * @throws AccessDeniedException
     * @throws CacheException
     * @throws EntityNotFoundException
     * @throws InvalidArgumentException
     */
    public function readById(int $id, UserInterface $user): array
    {
        /** @var CacheItem $cacheItem */
        $cacheItem = $this->cache->getItem(sprintf('completed_task__%s', $id));
        if ($cacheItem->isHit()) {
            $completedTask = $cacheItem->get();
        } else {
            $completedTask = $this->entityManager->getRepository(CompletedTask::class)->find($id);
        }

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
            $teacher = $this->entityManager->getRepository(Teacher::class)->findOneBy(['user' => $user->getId()]);

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

        $result = $completedTask->toArray();
        $cacheItem->set($result);
        $cacheItem->tag((string) $student->getId());
        $this->cache->save($cacheItem);

        return $result;
    }

    /**
     * @throws AccessDeniedException
     */
    private function getCompletedTasks(ReadData $dto, UserInterface $user): array
    {
        if (array_intersect([StaffManager::ROLE_STAFF, StudentManager::ROLE_STUDENT], $user->getRoles())) {
            throw new AccessDeniedException("You can't do this action");
        }

        if ($user->hasRole(StudentManager::ROLE_STUDENT)) {
            /** @var Student $student */
            $student = $this->entityManager->getRepository(Student::class)->findOneBy([['user' => $user]]);
            $dto->studentId = $student->getId();
        }

        /** @var CacheItem $cacheItem */
        $cacheItem = $this->cache->getItem(sprintf('completed_task__%s', md5(json_encode((array) $dto))));
        if ($cacheItem->isHit()) {
            return $cacheItem->get();
        }

        $result = array_map(
            static fn(CompletedTask $ct) => $ct->toArray(),
            $this->entityManager->getRepository(CompletedTask::class)->read($dto)->toArray()
        );
        $cacheItem->set($result);
        $cacheItem->expiresAt((new DateTime('now'))->modify('+1 hour'));
        $this->cache->save($cacheItem);

        return $result;
    }
}
