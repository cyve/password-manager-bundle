<?php

namespace Cyve\PasswordManagerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ResetPasswordController extends AbstractController
{
    /**
     * @Route("/password/reset", name="cyve_password_manager_reset_password")
     */
    public function __invoke()
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the login_link key on your firewall.');
    }
}
