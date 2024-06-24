<?php

namespace App\Service;

use App\Collection\EntityCollection;
use App\Controller\Api\v1\Percentage\Input\ReadData;
use App\Entity\CuratedCourse;
use App\Entity\Student;
use App\Entity\Subscription;
use App\Entity\Task;
use App\Entity\Teacher;
use App\Exception\AccessDeniedException;
use App\Exception\BadRequestException;
use App\Exception\EntityNotFoundException;
use App\Manager\StudentManager;
use App\Manager\TeacherManager;
use App\Repository\PercentageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class PercentageService
{

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly PercentageRepository $percentageRepository
    ) {

    }

    /**
     * @throws BadRequestException
     * @throws EntityNotFoundException
     * @throws AccessDeniedException
     */
    public function getPercentages(ReadData $dto, UserInterface $user): EntityCollection
    {
        /** @var Task|null $task */
        $task = $this->entityManager->getRepository(Task::class)->find($dto->taskId);
        if ($task === null) {
            throw new EntityNotFoundException('Task not found');
        }

        $lesson = $task->getLesson();
        $course = $lesson->getCourse();

        if ($user->hasRole(StudentManager::ROLE_STUDENT)) {
            $student = $this->entityManager->getRepository(Student::class)->findOneBy([['user' => $user]]);

            $flag = true;
            /** @var Subscription $subscription */
            foreach ($course->getSubscriptions()->toArray() as $subscription) {
                if ($subscription->getStudent() === $student) {
                    $flag = false;
                    break;
                }
            }

            if ($flag) {
                throw new AccessDeniedException("You can't do this action");
            }
        }

        if ($user->hasRole(TeacherManager::ROLE_TEACHER)) {
            $teacher = $this->entityManager->getRepository(Teacher::class)->findOneBy([['user' => $user]]);

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

        return $this->percentageRepository->getPercentages($dto);
    }
}
