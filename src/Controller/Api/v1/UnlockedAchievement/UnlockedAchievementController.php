<?php

namespace App\Controller\Api\v1\UnlockedAchievement;

use App\Collection\EntityCollection;
use App\Controller\Api\v1\UnlockedAchievement\Input\CreateData;
use App\Controller\Api\v1\UnlockedAchievement\Input\ReadData;
use App\Controller\Api\v1\UnlockedAchievement\Input\UpdateData;
use App\Entity\UnlockedAchievement;
use App\Exception\BadRequestException;
use App\Exception\EntityNotFoundException;
use App\Manager\UnlockedAchievementManager;
use App\Repository\UnlockedAchievementRepository;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/api/v1/unlocked-achievement')]
class UnlockedAchievementController extends AbstractController
{

    public function __construct(
        private readonly UnlockedAchievementManager $unlockedAchievementManager,
        private readonly UnlockedAchievementRepository $unlockedAchievementRepository
    ) {

    }

    /**
     * @throws Exception
     */
    #[Route(path: '', methods: ['POST'])]
    public function create(#[MapRequestPayload] CreateData $dto): UnlockedAchievement
    {
        return $this->unlockedAchievementManager->give($dto);
    }

    /**
     * @throws EntityNotFoundException|BadRequestException
     */
    #[Route(path: '', methods: ['PATCH'])]
    public function update(#[MapRequestPayload] UpdateData $dto): UnlockedAchievement
    {
        return $this->unlockedAchievementManager->update($dto);
    }

    /**
     * @throws EntityNotFoundException
     */
    #[Route(path: '/{id}', requirements: ['id' => '\d+'], methods: ['DELETE'])]
    public function delete(int $id)
    {
        return $this->unlockedAchievementManager->delete($id);
    }

    /**
     * @throws EntityNotFoundException
     */
    #[Route(path: '', methods: ['GET'])]
    public function read(#[MapQueryString] ReadData $dto): EntityCollection
    {
        return $this->unlockedAchievementRepository->read($dto);
    }
}
