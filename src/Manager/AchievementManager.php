<?php

namespace App\Manager;

use App\Controller\Api\v1\Achievement\Input\CreateData;
use App\Controller\Api\v1\Achievement\Input\UpdateData;
use App\Entity\Achievement;
use App\Exception\BadRequestException;
use App\Exception\EntityNotFoundException;
use App\Repository\AchievementRepository;
use Doctrine\ORM\EntityManagerInterface;
use Throwable;

class AchievementManager
{

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly AchievementRepository $achievementRepository
    ) {
    }

    /**
     * @throws BadRequestException
     */
    public function create(CreateData $dto): Achievement
    {
        $achievement = (new Achievement())->setName($dto->name);
        $this->entityManager->persist($achievement);
        try {
            $this->entityManager->flush();
        } catch (Throwable $e) {
            throw new BadRequestException($e->getMessage());
        }

        return $achievement;
    }

    /**
     * @throws EntityNotFoundException
     * @throws BadRequestException
     */
    public function update(UpdateData $dto): Achievement
    {
        $achievement = $this->achievementRepository->find($dto->id);
        if ($achievement === null) {
            throw new EntityNotFoundException();
        }

        $achievement->setName($dto->name);
        try {
            $this->entityManager->flush();
        } catch (Throwable $e) {
            throw new BadRequestException($e->getMessage());
        }

        return $achievement;
    }

    /**
     * @throws EntityNotFoundException
     */
    public function delete(int $id)
    {
        $achievement = $this->achievementRepository->find($id);
        if ($achievement === null) {
            throw new EntityNotFoundException();
        }

        $this->entityManager->remove($achievement);
        $this->entityManager->flush();

        return null;
    }
}
