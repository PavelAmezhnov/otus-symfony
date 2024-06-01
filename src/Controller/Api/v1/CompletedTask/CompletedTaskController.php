<?php

namespace App\Controller\Api\v1\CompletedTask;

use App\Collection\EntityCollection;
use App\Controller\Api\v1\CompletedTask\Input\CreateData;
use App\Controller\Api\v1\CompletedTask\Input\ReadData;
use App\Controller\Api\v1\CompletedTask\Input\UpdateData;
use App\Entity\CompletedTask;
use App\Exception\BadRequestException;
use App\Exception\EntityNotFoundException;
use App\Manager\CompletedTaskManager;
use App\Service\CompletedTaskService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/api/v1/completed-task')]
class CompletedTaskController extends AbstractController
{

    public function __construct(
        private readonly CompletedTaskService $completedTaskService,
        private readonly CompletedTaskManager $completedTaskManager
    ) {
    }

    /**
     * @throws EntityNotFoundException
     * @throws BadRequestException
     */
    #[Route(path: '', methods: ['POST'])]
    public function create(#[MapRequestPayload] CreateData $dto): CompletedTask
    {
        return $this->completedTaskManager->create($dto);
    }

    /**
     * @throws EntityNotFoundException
     */
    #[Route(path: '', methods: ['PATCH'])]
    public function update(#[MapRequestPayload] UpdateData $dto): CompletedTask
    {
        return $this->completedTaskManager->update($dto);
    }

    /**
     * @throws EntityNotFoundException
     */
    #[Route(path: '/{id}', requirements: ['id' => '\d+'], methods: ['DELETE'])]
    public function delete(int $id)
    {
        return $this->completedTaskManager->delete($id);
    }

    /**
     * @throws EntityNotFoundException|BadRequestException
     */
    #[Route(path: '', methods: ['GET'])]
    public function read(#[MapQueryString] ReadData $dto): int|float|EntityCollection
    {
        return $this->completedTaskService->read($dto);
    }
}
