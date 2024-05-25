<?php

namespace App\Controller\Api\v1\Course;

use App\Collection\EntityCollection;
use App\Controller\Api\v1\Course\Input\CreateData;
use App\Controller\Api\v1\Course\Input\ReadData;
use App\Controller\Api\v1\Course\Input\UpdateData;
use App\Entity\Course;
use App\Exception\BadRequestException;
use App\Exception\EntityNotFoundException;
use App\Manager\CourseManager;
use App\Repository\CourseRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/api/v1/course')]
class CourseController extends AbstractController
{

    public function __construct(
        private readonly CourseManager $courseManager,
        private readonly CourseRepository $courseRepository
    ) {

    }

    /**
     * @throws BadRequestException
     */
    #[Route(path: '', methods: ['POST'])]
    public function create(#[MapRequestPayload] CreateData $dto): Course
    {
        return $this->courseManager->create($dto);
    }

    /**
     * @throws EntityNotFoundException|BadRequestException
     */
    #[Route(path: '', methods: ['PATCH'])]
    public function update(#[MapRequestPayload] UpdateData $dto): Course
    {
        return $this->courseManager->update($dto);
    }

    /**
     * @throws EntityNotFoundException
     */
    #[Route(path: '/{id}', requirements: ['id' => '\d+'], methods: ['DELETE'])]
    public function delete(int $id)
    {
        return $this->courseManager->delete($id);
    }

    #[Route(path: '', methods: ['GET'])]
    public function read(#[MapQueryString] ReadData $dto): EntityCollection
    {
        return $this->courseRepository->read($dto);
    }

    /**
     * @throws EntityNotFoundException
     */
    #[Route(path: '/{id}', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function readById(int $id): array
    {
        return $this->courseRepository->readById($id);
    }
}
