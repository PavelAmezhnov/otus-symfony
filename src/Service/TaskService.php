<?php

namespace App\Service;

use App\Collection\EntityCollection;
use App\Controller\Api\v1\Task\Input\CreateData;
use App\Controller\Api\v1\Task\Input\ReadData;
use App\Controller\Api\v1\Task\Input\UpdateData;
use App\Entity\CuratedCourse;
use App\Entity\Lesson;
use App\Entity\Student;
use App\Entity\Subscription;
use App\Entity\Task;
use App\Entity\Teacher;
use App\Exception\AccessDeniedException;
use App\Exception\BadRequestException;
use App\Exception\EntityNotFoundException;
use App\Manager\StaffManager;
use App\Manager\StudentManager;
use App\Manager\TaskManager;
use App\Manager\TeacherManager;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class TaskService
{

    public function __construct(
        private readonly TaskManager $taskManager,
        private readonly TaskRepository $taskRepository,
        private readonly EntityManagerInterface $entityManager
    ) {

    }

    /**
     * @throws EntityNotFoundException
     * @throws AccessDeniedException
     * @throws BadRequestException
     */
    public function create(CreateData $dto, UserInterface $user): Task
    {
        if ($user->hasRole(TeacherManager::ROLE_TEACHER)) {
            /** @var Teacher|null $teacher */
            $teacher = $this->entityManager->getRepository(Teacher::class)->findOneBy(['user' => $user]);
            if ($teacher === null) {
                throw new AccessDeniedException("You can't do this action");
            }

            /** @var Lesson|null $lesson */
            $lesson = $this->entityManager->getRepository(Lesson::class)->find($dto->lessonId);
            if ($lesson === null) {
                throw new EntityNotFoundException('Lesson not found');
            }

            $course = $lesson->getCourse();
            /** @var CuratedCourse $curatedCourse */
            foreach ($course->getCuratedCourses()->toArray() as $curatedCourse) {
                if ($curatedCourse->getTeacher() === $teacher) {
                    return $this->taskManager->create($dto);
                }
            }
        }

        throw new AccessDeniedException("You can't do this action");
    }

    /**
     * @throws EntityNotFoundException
     * @throws AccessDeniedException
     * @throws BadRequestException
     */
    public function update(UpdateData $dto, UserInterface $user): Task
    {
        if ($user->hasRole(TeacherManager::ROLE_TEACHER)) {
            /** @var Teacher|null $teacher */
            $teacher = $this->entityManager->getRepository(Teacher::class)->findOneBy(['user' => $user]);
            if ($teacher === null) {
                throw new AccessDeniedException("You can't do this action");
            }

            /** @var Task|null $task */
            $task = $this->taskRepository->find($dto->id);
            if ($task === null) {
                throw new EntityNotFoundException('Task not found');
            }

            $lesson = $task->getLesson();
            $course = $lesson->getCourse();

            /** @var CuratedCourse $curatedCourse */
            foreach ($course->getCuratedCourses()->toArray() as $curatedCourse) {
                if ($curatedCourse->getTeacher() === $teacher) {
                    return $this->taskManager->update($dto);
                }
            }
        }

        throw new AccessDeniedException("You can't do this action");
    }

    /**
     * @throws EntityNotFoundException
     * @throws AccessDeniedException
     */
    public function delete(int $id, UserInterface $user)
    {
        if ($user->hasRole(TeacherManager::ROLE_TEACHER)) {
            /** @var Teacher|null $teacher */
            $teacher = $this->entityManager->getRepository(Teacher::class)->findOneBy(['user' => $user]);
            if ($teacher === null) {
                throw new AccessDeniedException("You can't do this action");
            }

            /** @var Task|null $task */
            $task = $this->taskRepository->find($id);
            if ($task === null) {
                throw new EntityNotFoundException('Task not found');
            }

            $lesson = $task->getLesson();
            $course = $lesson->getCourse();

            /** @var CuratedCourse $curatedCourse */
            foreach ($course->getCuratedCourses()->toArray() as $curatedCourse) {
                if ($curatedCourse->getTeacher() === $teacher) {
                    return $this->taskManager->delete($id);
                }
            }
        }

        throw new AccessDeniedException("You can't do this action");
    }

    /**
     * @throws AccessDeniedException
     * @throws EntityNotFoundException
     * @throws BadRequestException
     */
    public function getTasks(ReadData $dto, UserInterface $user): EntityCollection
    {
        if ($user->hasRole(TeacherManager::ROLE_TEACHER)) {
            /** @var Teacher|null $teacher */
            $teacher = $this->entityManager->getRepository(Teacher::class)->findOneBy(['user' => $user]);
            if ($teacher === null) {
                throw new AccessDeniedException("You can't do this action");
            }

            if ($dto->lessonId === null) {
                throw new BadRequestException('lessonId is required');
            }

            /** @var Lesson|null $lesson */
            $lesson = $this->entityManager->getRepository(Lesson::class)->find($dto->lessonId);
            if ($lesson === null) {
                throw new EntityNotFoundException('Lesson not found');
            }

            $course = $lesson->getCourse();
            /** @var CuratedCourse $curatedCourse */
            foreach ($course->getCuratedCourses()->toArray() as $curatedCourse) {
                if ($curatedCourse->getTeacher() === $teacher) {
                    return $this->taskRepository->getTasks($dto);
                }
            }
        } elseif ($user->hasRole(StudentManager::ROLE_STUDENT)) {
            /** @var Student|null $student */
            $student = $this->entityManager->getRepository(Student::class)->findOneBy(['user' => $user]);
            if ($student === null) {
                throw new AccessDeniedException("You can't do this action");
            }

            if ($dto->lessonId === null) {
                throw new BadRequestException('lessonId is required');
            }

            /** @var Lesson|null $lesson */
            $lesson = $this->entityManager->getRepository(Lesson::class)->find($dto->lessonId);
            if ($lesson === null) {
                throw new EntityNotFoundException('Lesson not found');
            }

            $course = $lesson->getCourse();
            /** @var Subscription $subscription */
            foreach ($course->getSubscriptions()->toArray() as $subscription) {
                if ($subscription->getStudent() === $student) {
                    return $this->taskRepository->getTasks($dto);
                }
            }
        } elseif ($user->hasRole(StaffManager::ROLE_STAFF)) {
            return $this->taskRepository->getTasks($dto);
        }

        throw new AccessDeniedException("You can't do this action");
    }
}
