<?php

namespace App\Controller\Api\v1\Task;

use App\Collection\EntityCollection;
use App\Controller\Api\v1\Task\Input\ReadData;
use App\Controller\Api\v1\Task\Input\CreateData;
use App\Controller\Api\v1\Task\Input\UpdateData;
use App\Entity\Task;
use App\Exception\AccessDeniedException;
use App\Exception\BadRequestException;
use App\Exception\EntityNotFoundException;
use App\Service\TaskService;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;

#[Route(path: '/api/v1/task')]
class TaskController extends AbstractController
{

    public function __construct(
        private readonly TaskService $taskService
    ) {

    }

    /**
     * @throws Exception
     */
    #[Route(path: '', methods: ['POST'])]
    public function create(#[MapRequestPayload] CreateData $dto, UserInterface $user): Task
    {
        return $this->taskService->create($dto, $user);
    }

    /**
     * @throws EntityNotFoundException|BadRequestException|AccessDeniedException
     */
    #[Route(path: '', methods: ['PATCH'])]
    public function update(#[MapRequestPayload] UpdateData $dto, UserInterface $user): Task
    {
        return $this->taskService->update($dto, $user);
    }

    /**
     * @throws EntityNotFoundException
     * @throws AccessDeniedException
     */
    #[Route(path: '/{id}', requirements: ['id' => '\d+'], methods: ['DELETE'])]
    public function delete(int $id, UserInterface $user)
    {
        return $this->taskService->delete($id, $user);
    }

    /**
     * @throws EntityNotFoundException
     * @throws BadRequestException
     * @throws AccessDeniedException
     */
    #[Route(path: '', methods: ['GET'])]
    public function read(#[MapQueryString] ReadData $dto, UserInterface $user): EntityCollection
    {
        return $this->taskService->getTasks($dto, $user);
    }
}
