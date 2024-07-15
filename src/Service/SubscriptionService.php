<?php

namespace App\Service;

use App\Collection\EntityCollection;
use App\Controller\Api\v1\Subscription\Input\CreateData;
use App\Controller\Api\v1\Subscription\Input\ReadData;
use App\Entity\Course;
use App\Entity\CuratedCourse;
use App\Entity\Student;
use App\Entity\Subscription;
use App\Entity\Teacher;
use App\Exception\AccessDeniedException;
use App\Exception\BadRequestException;
use App\Exception\EntityNotFoundException;
use App\Manager\StaffManager;
use App\Manager\StudentManager;
use App\Manager\SubscriptionManager;
use App\Manager\TeacherManager;
use App\Repository\SubscriptionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class SubscriptionService
{

    public function __construct(
        private readonly SubscriptionManager $subscriptionManager,
        private readonly EntityManagerInterface $entityManager,
        private readonly SubscriptionRepository $subscriptionRepository
    ) {
    }

    /**
     * @throws EntityNotFoundException
     * @throws BadRequestException
     * @throws AccessDeniedException
     */
    public function subscribe(CreateData $dto, UserInterface $user): Subscription
    {
        if ($user->hasRole(StudentManager::ROLE_STUDENT)) {
            /** @var Student|null $student */
            $student = $this->entityManager->getRepository(Student::class)->findOneBy(['user' => $user]);
            if ($student === null) {
                throw new EntityNotFoundException('Student not found');
            }

            if ($student->getId() === $dto->studentId) {
                return $this->subscriptionManager->subscribe($dto);
            }
        } elseif ($user->hasRole(StaffManager::ROLE_ADMIN)) {
            return $this->subscriptionManager->subscribe($dto);
        }

        throw new AccessDeniedException("You can't do this action");
    }

    /**
     * @throws EntityNotFoundException
     * @throws AccessDeniedException
     */
    public function delete(int $id, UserInterface $user)
    {
        if ($user->hasRole(StudentManager::ROLE_STUDENT)) {
            /** @var Subscription|null $subscription */
            $subscription = $this->subscriptionRepository->find($id);
            if ($subscription === null) {
                throw new EntityNotFoundException('Subscription not found');
            }

            if ($subscription->getStudent()->getUser() === $user) {
                return $this->subscriptionManager->delete($id);
            }
        } elseif ($user->hasRole(StaffManager::ROLE_ADMIN)) {
            return $this->subscriptionManager->delete($id);
        }

        throw new AccessDeniedException("You can't do this action");
    }

    /**
     * @throws BadRequestException
     * @throws AccessDeniedException
     * @throws EntityNotFoundException
     */
    public function getSubscriptions(ReadData $dto, UserInterface $user): EntityCollection
    {
        if ($user->hasRole(StudentManager::ROLE_STUDENT)) {
            if ($dto->studentId === null) {
                throw new BadRequestException('studentId is required');
            }

            /** @var Student|null $student */
            $student = $this->entityManager->getRepository(Student::class)->findOneBy(['user' => $user]);
            if ($student === null) {
                throw new EntityNotFoundException('Student not found');
            }

            if ($student->getId() === $dto->studentId) {
                return $this->subscriptionRepository->getSubscriptions($dto);
            }
        } elseif ($user->hasRole(TeacherManager::ROLE_TEACHER)) {
            if ($dto->courseId === null) {
                throw new BadRequestException('courseId is required');
            }

            /** @var Course|null $course */
            $course = $this->entityManager->getRepository(Course::class)->find($dto->courseId);
            if ($course === null) {
                throw new EntityNotFoundException('Course not found');
            }

            /** @var Teacher|null $teacher */
            $teacher = $this->entityManager->getRepository(Teacher::class)->findOneBy(['user' => $user]);
            if ($teacher === null) {
                throw new AccessDeniedException("You can't do this action");
            }

            /** @var CuratedCourse $curatedCourse */
            foreach ($course->getCuratedCourses()->toArray() as $curatedCourse) {
                if ($curatedCourse->getTeacher() === $teacher) {
                    return $this->subscriptionRepository->getSubscriptions($dto);
                }
            }
        } elseif ($user->hasRole(StaffManager::ROLE_STAFF)) {
            return $this->subscriptionRepository->getSubscriptions($dto);
        }

        throw new AccessDeniedException("You can't do this action");
    }
}
