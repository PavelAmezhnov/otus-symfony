<?php

namespace App\Manager;

use App\Controller\Api\v1\User\Input\CreateData;
use App\Entity\User;
use App\Exception\BadRequestException;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Throwable;

class UserManager
{

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly UserPasswordHasherInterface $userPasswordHasher
    ) {
    }

    /**
     * @throws BadRequestException
     * @throws Exception
     */
    public function create(CreateData $dto): User
    {
        $user = new User();
        $user->setLogin($dto->login)
            ->setPassword($this->userPasswordHasher->hashPassword($user, $dto->password))
            ->setToken($this->generateToken());

        $this->entityManager->persist($user);
        try {
            $this->entityManager->flush();
        } catch (Throwable $e) {
            throw new BadRequestException($e->getMessage());
        }

        return $user;
    }

    /**
     * @throws Exception
     */
    public function updateToken(string $login): ?string
    {
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['login' => $login]);
        if ($user === null) {
            return null;
        }
        $token = $this->generateToken();
        $user->setToken($token);
        $this->entityManager->flush();

        return $token;
    }

    /**
     * @throws Exception
     */
    private function generateToken(): string
    {
        return base64_encode(random_bytes(20));
    }
}
