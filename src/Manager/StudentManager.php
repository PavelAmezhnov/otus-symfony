<?php

namespace App\Manager;

use App\Controller\Api\v1\Student\Input\CreateData;
use App\Controller\Api\v1\Student\Input\UpdateData;
use App\Entity\Student;
use App\Exception\EntityNotFoundException;
use App\Repository\StudentRepository;
use Doctrine\ORM\EntityManagerInterface;

class StudentManager
{

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly StudentRepository $studentRepository
    ) {
    }

    public function create(CreateData $data): Student
    {
        $student = (new Student())
            ->setFirstName($data->firstName)
            ->setLastName($data->lastName);
        $this->entityManager->persist($student);
        $this->entityManager->flush();

        return $student;
    }

    /**
     * @throws EntityNotFoundException
     */
    public function update(UpdateData $data): Student
    {
        $student = $this->studentRepository->find($data->id);
        if ($student === null) {
            throw new EntityNotFoundException();
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
