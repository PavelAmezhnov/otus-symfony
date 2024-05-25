<?php

namespace App\Controller\Api\v1\Task;

use App\Collection\EntityCollection;
use App\Controller\Api\v1\Task\Input\ReadData;
use App\Controller\Api\v1\Task\Input\CreateData;
use App\Controller\Api\v1\Task\Input\UpdateData;
use App\Entity\Task;
use App\Exception\BadRequestException;
use App\Exception\EntityNotFoundException;
use App\Manager\TaskManager;
use App\Repository\TaskRepository;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/api/v1/task')]
class TaskController extends AbstractController
{

    public function __construct(
        private readonly TaskManager $taskManager,
        private readonly TaskRepository $taskRepository
    ) {

    }

    /**
     * @throws Exception
     */
    #[Route(path: '', methods: ['POST'])]
    public function create(#[MapRequestPayload] CreateData $dto): Task
    {
        return $this->taskManager->create($dto);
    }

    /**
     * @throws EntityNotFoundException|BadRequestException
     */
    #[Route(path: '', methods: ['PATCH'])]
    public function update(#[MapRequestPayload] UpdateData $dto): Task
    {
        return $this->taskManager->update($dto);
    }

    /**
     * @throws EntityNotFoundException
     */
    #[Route(path: '/{id}', requirements: ['id' => '\d+'], methods: ['DELETE'])]
    public function delete(int $id)
    {
        return $this->taskManager->delete($id);
    }

    /**
     * @throws EntityNotFoundException
     */
    #[Route(path: '', methods: ['GET'])]
    public function read(#[MapQueryString] ReadData $dto): EntityCollection
    {
        return $this->taskRepository->getTasks($dto);
    }
}
