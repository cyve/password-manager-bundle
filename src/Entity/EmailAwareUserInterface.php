<?php

namespace Cyve\PasswordManagerBundle\Entity;

use Symfony\Component\Security\Core\User\UserInterface;

interface EmailAwareUserInterface extends UserInterface
{
    public function getEmail(): ?string;
}
