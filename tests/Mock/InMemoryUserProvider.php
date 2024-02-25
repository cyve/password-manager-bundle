<?php

namespace Cyve\PasswordManagerBundle\Tests\Mock;

use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\InMemoryUser;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class InMemoryUserProvider implements UserProviderInterface, PasswordUpgraderInterface
{
    private array $users = [];

    public function __construct()
    {
        $this->users['lorem@mail.com'] = new InMemoryUser('lorem@mail.com', 'lorem');
    }

    public function refreshUser(UserInterface $user): UserInterface
    {
        return $this->loadUserByIdentifier($user->getUserIdentifier());
    }

    public function supportsClass(string $class): bool
    {
        return $class === InMemoryUser::class;
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
        \Closure::bind(fn () => $this->password = $newHashedPassword, $user, InMemoryUser::class)();
    }
}
