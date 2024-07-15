<?php

namespace App\Manager;

use App\Controller\Api\v1\Teacher\Input\CreateData;
use App\Entity\Teacher;
use App\Entity\User;
use App\Exception\BadRequestException;
use App\Exception\EntityNotFoundException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Throwable;

class TeacherManager
{

    public const ROLE_TEACHER = 'ROLE_TEACHER';

    public function __construct(
        private readonly EntityManagerInterface $entityManager
    ) {

    }

    /**
     * @throws BadRequestException
     * @throws EntityNotFoundException
     */
    public function create(CreateData $dto): Teacher
    {
        /** @var User|null $user */
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['login' => $dto->userLogin]);
        if ($user === null) {
            throw new EntityNotFoundException('User not found');
        }

        if ($user->hasRole(self::ROLE_TEACHER)) {
            throw new BadRequestException('Teacher have already created');
        }
        $user->setRoles(array_unique(array_merge($user->getRoles(), [self::ROLE_TEACHER])));

        $teacher = (new Teacher())
            ->setFirstName($dto->firstName)
            ->setLastName($dto->lastName)
            ->setUser($user)
        ;
        $this->entityManager->persist($teacher);

        try {
            $this->entityManager->flush();
        } catch (Throwable $e) {
            throw new BadRequestException($e->getMessage());
        }

        return $teacher;
    }
}
