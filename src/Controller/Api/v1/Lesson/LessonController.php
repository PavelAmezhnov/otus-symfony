<?php

namespace App\Controller\Api\v1\Lesson;

use App\Collection\EntityCollection;
use App\Controller\Api\v1\Lesson\Input\ReadData;
use App\Controller\Api\v1\Lesson\Input\UpdateData;
use App\Controller\Api\v1\Lesson\Input\CreateData;
use App\Entity\Lesson;
use App\Exception\BadRequestException;
use App\Exception\EntityNotFoundException;
use App\Manager\LessonManager;
use App\Repository\LessonRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/api/v1/lesson')]
class LessonController extends AbstractController
{

    public function __construct(
        private readonly LessonManager $lessonManager,
        private readonly LessonRepository $lessonRepository
    ) {

    }

    /**
     * @throws BadRequestException
     * @throws EntityNotFoundException
     */
    #[Route(path: '', methods: ['POST'])]
    public function create(#[MapRequestPayload] CreateData $dto): Lesson
    {
        return $this->lessonManager->create($dto);
    }

    /**
     * @throws EntityNotFoundException
     * @throws BadRequestException
     */
    #[Route(path: '', methods: ['PATCH'])]
    public function update(#[MapRequestPayload] UpdateData $dto): Lesson
    {
        return $this->lessonManager->update($dto);
    }

    /**
     * @throws EntityNotFoundException
     */
    #[Route(path: '/{id}', requirements: ['id' => '\d+'], methods: ['DELETE'])]
    public function delete(int $id)
    {
        return $this->lessonManager->delete($id);
    }

    /**
     * @throws BadRequestException
     */
    #[Route(path: '', methods: ['GET'])]
    public function read(#[MapQueryString] ReadData $dto): EntityCollection
    {
        return $this->lessonRepository->getLessons($dto);
    }
}
