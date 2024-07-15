<?php

namespace App\Service;

use App\Collection\EntityCollection;
use App\Controller\Api\v1\Student\Input\ReadData;
use App\Controller\Api\v1\Student\Input\UpdateData;
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
use App\Manager\TeacherManager;
use App\Repository\StudentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class StudentService
{

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly StudentRepository $studentRepository,
        private readonly StudentManager $studentManager
    ) {

    }

    /**
     * @throws BadRequestException
     * @throws EntityNotFoundException
     * @throws AccessDeniedException
     */
    public function getStudents(ReadData $dto, UserInterface $user): EntityCollection
    {
        if ($user->hasRole(StudentManager::ROLE_STUDENT)) {
            throw new AccessDeniedException("You can't do this action");
        }

        if ($user->hasRole(TeacherManager::ROLE_TEACHER)) {
            if ($dto->courseId === null) {
                throw new AccessDeniedException("courseId is required");
            }

            /** @var Course|null $course */
            $course = $this->entityManager->getRepository(Course::class)->find($dto->courseId);
            if ($course === null) {
                throw new EntityNotFoundException('Course not found');
            }

            $teacher = $this->entityManager->getRepository(Teacher::class)->findOneBy(['user' => $user]);
            if ($teacher === null) {
                throw new EntityNotFoundException('Teacher not found');
            }

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

        return $this->studentRepository->getStudents($dto);
    }

    /**
     * @throws AccessDeniedException
     * @throws EntityNotFoundException
     */
    public function getStudentById(int $id, UserInterface $user): Student
    {
        /** @var Student|null $student */
        $student = $this->studentRepository->find($id);
        if ($student === null) {
            throw new EntityNotFoundException('Student not found');
        }

        if ($user->hasRole(StudentManager::ROLE_STUDENT) && $student->getUser() !== $user) {
            throw new AccessDeniedException("You can't do this action");
        }

        if ($user->hasRole(TeacherManager::ROLE_TEACHER)) {
            $teacher = $this->entityManager->getRepository(Teacher::class)->findOneBy(['user' => $user]);
            if ($teacher === null) {
                throw new EntityNotFoundException('Teacher not found');
            }

            $flag = true;
            /** @var Subscription $subscription */
            foreach ($student->getSubscriptions()->toArray() as $subscription) {
                $course = $subscription->getCourse();
                /** @var CuratedCourse $curatedCourse */
                foreach ($course->getCuratedCourses() as $curatedCourse) {
                    if ($curatedCourse->getTeacher() === $teacher) {
                        $flag = false;
                        break;
                    }
                }
            }

            if ($flag) {
                throw new AccessDeniedException("You can't do this action");
            }
        }

        return $this->studentRepository->getStudentById($id);
    }

    /**
     * @throws AccessDeniedException
     * @throws EntityNotFoundException
     */
    public function update(UpdateData $dto, UserInterface $user): Student
    {
        if ($user->hasRole(TeacherManager::ROLE_TEACHER)) {
            throw new AccessDeniedException("You can't do this action");
        }

        if ($user->hasRole(StudentManager::ROLE_STUDENT)) {
            /** @var Student|null $student */
            $student = $this->studentRepository->find($dto->id);
            if ($student === null) {
                throw new EntityNotFoundException('Student not found');
            }

            if ($student->getUser() !== $user) {
                throw new AccessDeniedException("You can't do this action");
            }
        }

        return $this->studentManager->update($dto);
    }

    /**
     * @throws EntityNotFoundException
     * @throws AccessDeniedException
     */
    public function delete(int $id, UserInterface $user)
    {
        if ($user->hasRole(StudentManager::ROLE_STUDENT)) {
            /** @var Student|null $student */
            $student = $this->studentRepository->find($id);
            if ($student === null) {
                throw new EntityNotFoundException('Student not found');
            }

            if ($student->getUser() === $user) {
                return $this->studentManager->delete($id);
            }
        } elseif ($user->hasRole(StaffManager::ROLE_ADMIN)) {
            return $this->studentManager->delete($id);
        }

        throw new AccessDeniedException("You can't do this action");
    }
}
