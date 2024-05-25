<?php

namespace App\Controller\Api\v1\Achievement;

use App\Collection\EntityCollection;
use App\Controller\Api\v1\Achievement\Input\CreateData;
use App\Controller\Api\v1\Achievement\Input\ReadData;
use App\Controller\Api\v1\Achievement\Input\UpdateData;
use App\Entity\Achievement;
use App\Exception\BadRequestException;
use App\Exception\EntityNotFoundException;
use App\Manager\AchievementManager;
use App\Repository\AchievementRepository;
use App\Service\AchievementService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/api/v1/achievement')]
class AchievementController extends AbstractController
{

    public function __construct(
        private readonly AchievementManager $achievementManager,
        private readonly AchievementRepository $achievementRepository,
        private readonly AchievementService $achievementService
    ) {
    }

    /**
     * @throws BadRequestException
     */
    #[Route(path: '', methods: ['POST'])]
    public function create(#[MapRequestPayload] CreateData $dto): Achievement
    {
        return $this->achievementManager->create($dto);
    }

    /**
     * @throws EntityNotFoundException
     */
    #[Route(path: '', methods: ['GET'])]
    public function read(#[MapQueryString] ReadData $dto): EntityCollection
    {
        return $this->achievementRepository->getAchievements($dto);
    }

    /**
     * @throws EntityNotFoundException
     */
    #[Route(path: '/{id}', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function readById(int $id): array
    {
        return $this->achievementService->readById($id);
    }

    /**
     * @throws EntityNotFoundException|BadRequestException
     */
    #[Route(path: '', methods: ['PATCH'])]
    public function update(#[MapRequestPayload] UpdateData $dto): Achievement
    {
        return $this->achievementManager->update($dto);
    }

    /**
     * @throws EntityNotFoundException
     */
    #[Route(path: '/{id}', requirements: ['id' => '\d+'], methods: ['DELETE'])]
    public function delete(int $id)
    {
        return $this->achievementManager->delete($id);
    }
}
