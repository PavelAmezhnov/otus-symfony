<?php

namespace App\Manager;

use App\Controller\Api\v1\UnlockedAchievement\Input\CreateData;
use App\Controller\Api\v1\UnlockedAchievement\Input\UpdateData;
use App\Entity\Achievement;
use App\Entity\Student;
use App\Entity\UnlockedAchievement;
use App\Exception\BadRequestException;
use App\Exception\EntityNotFoundException;
use Doctrine\ORM\EntityManagerInterface;
use Throwable;

class UnlockedAchievementManager
{

    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    /**
     * @throws EntityNotFoundException
     * @throws BadRequestException
     */
    public function give(CreateData $dto): UnlockedAchievement
    {
        /** @var Student|null $student */
        $student = $this->entityManager->getRepository(Student::class)->find($dto->studentId);
        if ($student === null) {
            throw new EntityNotFoundException('Student not found');
        }

        /** @var Achievement|null $achievement */
        $achievement = $this->entityManager->getRepository(Achievement::class)->find($dto->achievementId);
        if ($achievement === null) {
            throw new EntityNotFoundException('Achievement not found');
        }

        $unlockedAchievement = (new UnlockedAchievement())
            ->setStudent($student)
            ->setAchievement($achievement);
        $this->entityManager->persist($unlockedAchievement);

        try {
            $this->entityManager->flush();
        } catch (Throwable $e) {
            throw new BadRequestException($e->getMessage());
        }

        return $unlockedAchievement;
    }

    /**
     * @throws EntityNotFoundException
     * @throws BadRequestException
     */
    public function update(UpdateData $dto): UnlockedAchievement
    {
        /** @var UnlockedAchievement|null $unlockedAchievement */
        $unlockedAchievement = $this->entityManager->getRepository(UnlockedAchievement::class)->find($dto->id);
        if ($unlockedAchievement === null) {
            throw new EntityNotFoundException('Unlocked achievement not found');
        }

        if ($dto->studentId !== null) {
            /** @var Student|null $student */
            $student = $this->entityManager->getRepository(Student::class)->find($dto->studentId);
            if ($student === null) {
                throw new EntityNotFoundException('Student not found');
            }

            $unlockedAchievement->setStudent($student);
        }

        if ($dto->achievementId !== null) {
            /** @var Achievement|null $achievement */
            $achievement = $this->entityManager->getRepository(Achievement::class)->find($dto->achievementId);
            if ($achievement === null) {
                throw new EntityNotFoundException('Achievement not found');
            }

            $unlockedAchievement->setAchievement($achievement);
        }

        try {
            $this->entityManager->flush();
        } catch (Throwable $e) {
            throw new BadRequestException($e->getMessage());
        }

        return $unlockedAchievement;
    }

    /**
     * @throws EntityNotFoundException
     */
    public function delete(int $id)
    {
        $unlockedAchievement = $this->entityManager->getRepository(UnlockedAchievement::class)->find($id);
        if ($unlockedAchievement === null) {
            throw new EntityNotFoundException('Unlocked achievement not found');
        }

        $this->entityManager->remove($unlockedAchievement);
        $this->entityManager->flush();

        return null;
    }
}
