<?php

namespace Cyve\PasswordManagerBundle\Tests\Mock;

use Cyve\PasswordManagerBundle\Entity\EmailAwareUserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class User implements UserInterface, EmailAwareUserInterface, PasswordAuthenticatedUserInterface
{
    private string $id;

    public function __construct(
        private string $email,
        private string $password,
        private array $roles = [],
    ) {
        $this->id = uniqid();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getRoles()
    {
        return $this->roles;
    }

    public function getUserIdentifier()
    {
        return $this->email;
    }

    public function getUsername()
    {
        return $this->email;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword($password): void
    {
        $this->password = $password;
    }

    public function getSalt()
    {
        return '';
    }

    public function eraseCredentials()
    {
    }
}