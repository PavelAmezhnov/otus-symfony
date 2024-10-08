<?php

namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTEncodeFailureException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AuthService
{

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly UserPasswordHasherInterface $userPasswordHasher,
        private readonly JWTEncoderInterface $jwtEncoder,
        private readonly int $tokenTTL
    ) {

    }

    public function isCredentialsValid(string $login, string $password): bool
    {
        /** @var User|null $user */
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['login' => $login]);
        if ($user === null) {
            return false;
        }

        return $this->userPasswordHasher->isPasswordValid($user, $password);
    }

    /**
     * @throws JWTEncodeFailureException
     */
    public function getJWTToken(string $login): string
    {
        /** @var User|null $user */
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['login' => $login]);
        $roles = $user ? $user->getRoles() : [];

        $tokenData = [
            'id' => $user->getId(),
            'login' => $login,
            'exp' => time() + $this->tokenTTL,
            'roles' => $roles
        ];

        return $this->jwtEncoder->encode($tokenData);
    }
}
