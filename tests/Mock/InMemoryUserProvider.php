<?php

namespace Cyve\PasswordManagerBundle\Tests\Mock;

use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class InMemoryUserProvider implements UserProviderInterface, PasswordUpgraderInterface
{
    private array $users = [];

    public function __construct()
    {
        $this->users['lorem@mail.com'] = new User('lorem@mail.com', 'lorem');
    }

    public function refreshUser(UserInterface $user)
    {
        return $this->loadUserByIdentifier($user->getUserIdentifier());
    }

    public function supportsClass(string $class)
    {
        return $class === User::class;
    }

    public function loadUserByIdentifier(string $username): UserInterface
    {
        if (!array_key_exists($username, $this->users)) {
            $exception = new UserNotFoundException();
            $exception->setUserIdentifier($username);

            throw $exception;
        }

        return $this->users[$username];
    }

    public function loadUserByUsername(string $username)
    {
        $this->loadUserByIdentifier($username);
    }

    public function upgradePassword(PasswordAuthenticatedUserInterface|UserInterface $user, string $newHashedPassword): void
    {
        $user->setPassword($newHashedPassword);
    }
}
