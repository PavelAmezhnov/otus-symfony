<?php

namespace App\Manager;

use App\Controller\Api\v1\Staff\Input\CreateData;
use App\Entity\Staff;
use App\Entity\User;
use App\Exception\BadRequestException;
use App\Exception\EntityNotFoundException;
use Doctrine\ORM\EntityManagerInterface;
use Throwable;

class StaffManager
{

    public const ROLE_STAFF = 'ROLE_STAFF';
    public const ROLE_ADMIN = 'ROLE_ADMIN';

    public function __construct(
        private readonly EntityManagerInterface $entityManager
    ) {

    }

    /**
     * @throws EntityNotFoundException|BadRequestException
     */
    public function create(CreateData $dto): Staff
    {
        /** @var User|null $user */
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['login' => $dto->userLogin]);
        if ($user === null) {
            throw new EntityNotFoundException('User not found');
        }

        $roles = array_merge($user->getRoles(), [self::ROLE_STAFF]);
        if ($dto->isAdmin === true) {
            $roles[] = self::ROLE_ADMIN;
        }
        $user->setRoles(array_unique($roles));

        $staff = (new Staff())
            ->setFirstName($dto->firstName)
            ->setLastName($dto->lastName)
            ->setUser($user);

        $this->entityManager->persist($staff);
        try {
            $this->entityManager->flush();
        } catch (Throwable $e) {
            throw new BadRequestException($e->getMessage());
        }

        return $staff;
    }
}
