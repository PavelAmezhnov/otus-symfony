<?php

namespace App\Manager;

use App\Controller\Api\v1\Student\Input\CreateData;
use App\Controller\Api\v1\Student\Input\UpdateData;
use App\Entity\Student;
use App\Exception\BadRequestException;
use App\Exception\EntityNotFoundException;
use App\Repository\StudentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Throwable;

class StudentManager
{

    public const ROLE_STUDENT = 'ROLE_STUDENT';

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly StudentRepository $studentRepository
    ) {
    }

    /**
     * @throws EntityNotFoundException
     * @throws BadRequestException
     */
    public function create(CreateData $data, UserInterface $user): Student
    {
        if ($user->hasRole(self::ROLE_STUDENT)) {
            throw new BadRequestException('Student have already created');
        }
        $user->setRoles(array_unique(array_merge($user->getRoles(), [self::ROLE_STUDENT])));

        $student = (new Student())
            ->setFirstName($data->firstName)
            ->setLastName($data->lastName)
            ->setUser($user)
        ;
        $this->entityManager->persist($student);

        try {
            $this->entityManager->flush();
        } catch (Throwable $e) {
            throw new BadRequestException($e->getMessage());
        }

        return $student;
    }

    /**
     * @throws EntityNotFoundException
     */
    public function update(UpdateData $data): Student
    {
        $student = $this->studentRepository->find($data->id);
        if ($student === null) {
            throw new EntityNotFoundException('Student not found');
        }

        $student->setFirstName($data->firstName);
        $student->setLastName($data->lastName);
        $this->entityManager->flush();

        return $student;
    }

    /**
     * @throws EntityNotFoundException
     */
    public function delete(int $id)
    {
        $student = $this->studentRepository->find($id);
        if ($student === null) {
            throw new EntityNotFoundException();
        }

        $this->entityManager->remove($student);
        $this->entityManager->flush();

        return null;
    }
}
