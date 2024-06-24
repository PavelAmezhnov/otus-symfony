<?php

namespace App\Security;

use Symfony\Component\Security\Core\User\UserInterface;

class AuthUser implements UserInterface
{
    private string $login;

    /** @var string[] */
    private array $roles;

    public function __construct(array $credentials)
    {
        $this->login = $credentials['login'];
        $this->roles = array_unique($credentials['roles']);
    }

    /**
     * @return string[]
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    public function getPassword(): string
    {
        return '';
    }

    public function getUserIdentifier(): string
    {
        return $this->login;
    }

    public function eraseCredentials(): void
    {
    }
}
