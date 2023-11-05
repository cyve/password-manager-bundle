<?php

namespace Cyve\PasswordManagerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ResetPasswordController extends AbstractController
{
    /**
     * @Route("/password/reset", name="cyve_password_manager_reset_password", methods={"GET"})
     */
    #[Route('/password/reset', name: 'cyve_password_manager_reset_password', methods: ['GET'])]
    public function __invoke(): Response
    {
    }
}
