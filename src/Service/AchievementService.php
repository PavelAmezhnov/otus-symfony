<?php

namespace App\Service;

use App\Entity\Student;
use App\Exception\EntityNotFoundException;
use App\Repository\AchievementRepository;
use Doctrine\ORM\EntityManagerInterface;

class AchievementService
{

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly AchievementRepository $achievementRepository
    ) {

    }

    /**
     * @throws EntityNotFoundException
     */
    public function readById(int $id): array
    {
        $achievement = $this->achievementRepository->find($id);
        if ($achievement === null) {
            throw new EntityNotFoundException('Achievement not found');
        }

        $result = $achievement->toArray();

        $countUnlockedAchievements = $achievement->getUnlockedAchievements()->count();
        $countStudents = count($this->entityManager->getRepository(Student::class)->findAll());
        $result['studentPercentageWithAchievement'] = $countStudents === 0
            ? 0.0
            : round(100 * ($countUnlockedAchievements / $countStudents), 1, PHP_ROUND_HALF_DOWN);

        return $result;
    }
}
