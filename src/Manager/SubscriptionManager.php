<?php

namespace App\Manager;

use App\Controller\Api\v1\Subscription\Input\CreateData;
use App\Controller\Api\v1\Subscription\Input\UpdateData;
use App\Entity\Course;
use App\Entity\Student;
use App\Entity\Subscription;
use App\Exception\BadRequestException;
use App\Exception\EntityNotFoundException;
use Doctrine\ORM\EntityManagerInterface;
use Throwable;

class SubscriptionManager
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    /**
     * @throws EntityNotFoundException
     * @throws BadRequestException
     */
    public function subscribe(CreateData $dto): Subscription
    {
        $student = $this->entityManager->getRepository(Student::class)->find($dto->studentId);
        if ($student === null) {
            throw new EntityNotFoundException('Student not found');
        }

        $course = $this->entityManager->getRepository(Course::class)->find($dto->courseId);
        if ($course === null) {
            throw new EntityNotFoundException('Course not found');
        }

        $subscription = (new Subscription())
            ->setStudent($student)
            ->setCourse($course);
        $this->entityManager->persist($subscription);

        try {
            $this->entityManager->flush();
        } catch (Throwable $e) {
            throw new BadRequestException($e->getMessage());
        }

        return $subscription;
    }

    /**
     * @throws EntityNotFoundException
     */
    public function update(UpdateData $dto): Subscription
    {
        /** @var Subscription|null $subscription */
        $subscription = $this->entityManager->getRepository(Subscription::class)->find($dto->id);
        if ($subscription === null) {
            throw new EntityNotFoundException('Subscription not found');
        }

        if ($dto->studentId !== null) {
            /** @var Student|null $student */
            $student = $this->entityManager->getRepository(Student::class)->find($dto->studentId);
            if ($student === null) {
                throw new EntityNotFoundException('Student not found');
            }

            $subscription->setStudent($student);
        }

        $this->entityManager->flush();

        return $subscription;
    }

    /**
     * @throws EntityNotFoundException
     */
    public function delete(int $id)
    {
        $subscription = $this->entityManager->getRepository(Subscription::class)->find($id);
        if ($subscription === null) {
            throw new EntityNotFoundException('Subscription not found');
        }

        $this->entityManager->remove($subscription);
        $this->entityManager->flush();

        return null;
    }
}
