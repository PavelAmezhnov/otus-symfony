<?php

namespace App\Security;

use Symfony\Component\Security\Core\User\UserInterface;

class AuthUser implements UserInterface
{
    private int $id;

    private string $login;

    /** @var string[] */
    private array $roles;

    public function __construct(array $credentials)
    {
        $this->id = $credentials['id'];
        $this->login = $credentials['login'];
        $this->roles = array_unique($credentials['roles']);
    }

    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string[]
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    public function hasRole(string $role): bool
    {
        return in_array($role, $this->roles, true);
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
