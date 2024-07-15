<?php

namespace App\Controller\Api\v1\Student;

use App\Collection\EntityCollection;
use App\Controller\Api\v1\Student\Input\CreateData;
use App\Controller\Api\v1\Student\Input\ReadData;
use App\Controller\Api\v1\Student\Input\UpdateData;
use App\Entity\Student;
use App\Exception\AccessDeniedException;
use App\Exception\BadRequestException;
use App\Exception\EntityNotFoundException;
use App\Manager\StudentManager;
use App\Service\StudentService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

#[Route(path: '/api/v1/student')]
class StudentController extends AbstractController
{

    public function __construct(
        private readonly StudentManager $studentManager,
        private readonly StudentService $studentService
    ) {
    }

    /**
     * @throws BadRequestException
     * @throws EntityNotFoundException
     */
    #[Route(path: '', methods: ['POST'])]
    public function create(#[MapRequestPayload] CreateData $dto, UserInterface $user): Student
    {
        return $this->studentManager->create($dto, $user);
    }

    /**
     * @throws EntityNotFoundException
     * @throws BadRequestException
     * @throws AccessDeniedException
     */
    #[Route(path: '', methods: ['GET'])]
    public function read(#[MapQueryString] ReadData $dto, UserInterface $user): EntityCollection
    {
        return $this->studentService->getStudents($dto, $user);
    }

    /**
     * @throws EntityNotFoundException
     * @throws AccessDeniedException
     */
    #[Route(path: '/{id}', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function readById(int $id, UserInterface $user): Student
    {
        return $this->studentService->getStudentById($id, $user);
    }

    /**
     * @throws EntityNotFoundException
     * @throws AccessDeniedException
     */
    #[Route(path: '', methods: ['PATCH'])]
    public function update(#[MapRequestPayload] UpdateData $dto, UserInterface $user): Student
    {
        return $this->studentService->update($dto, $user);
    }

    /**
     * @throws EntityNotFoundException
     * @throws AccessDeniedException
     */
    #[Route(path: '/{id}', requirements: ['id' => '\d+'], methods: ['DELETE'])]
    public function delete(int $id, UserInterface $user)
    {
        return $this->studentService->delete($id, $user);
    }
}
