<?php

namespace App\Controller\Api\v1\Student;

use App\Collection\EntityCollection;
use App\Controller\Api\v1\Student\Input\CreateData;
use App\Controller\Api\v1\Student\Input\ReadData;
use App\Controller\Api\v1\Student\Input\UpdateData;
use App\Entity\Student;
use App\Exception\BadRequestException;
use App\Exception\EntityNotFoundException;
use App\Manager\StudentManager;
use App\Repository\StudentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/api/v1/student')]
class StudentController extends AbstractController
{

    public function __construct(
        private readonly StudentManager $studentManager,
        private readonly StudentRepository $studentRepository
    ) {
    }

    #[Route(path: '', methods: ['POST'])]
    public function create(#[MapRequestPayload] CreateData $dto): Student
    {
        return $this->studentManager->create($dto);
    }

    /**
     * @throws EntityNotFoundException
     * @throws BadRequestException
     */
    #[Route(path: '', methods: ['GET'])]
    public function read(#[MapQueryString] ReadData $dto): EntityCollection
    {
        return $this->studentRepository->getStudents($dto);
    }

    /**
     * @throws EntityNotFoundException
     */
    #[Route(path: '/{id}', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function readById(int $id): Student
    {
        return $this->studentRepository->getStudentById($id);
    }

    /**
     * @throws EntityNotFoundException
     */
    #[Route(path: '', methods: ['PATCH'])]
    public function update(#[MapRequestPayload] UpdateData $dto): Student
    {
        return $this->studentManager->update($dto);
    }

    /**
     * @throws EntityNotFoundException
     */
    #[Route(path: '/{id}', requirements: ['id' => '\d+'], methods: ['DELETE'])]
    public function delete(int $id)
    {
        return $this->studentManager->delete($id);
    }
}
