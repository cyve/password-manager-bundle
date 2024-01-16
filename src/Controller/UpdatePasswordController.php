<?php

namespace Cyve\PasswordManagerBundle\Controller;

use Cyve\PasswordManagerBundle\Form\UpdatePasswordType;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class UpdatePasswordController extends AbstractController implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    #[Route('/password/update', name: 'cyve_password_manager_update_password', methods: ['GET', 'POST'])]
    public function __invoke(Request $request, UserPasswordHasherInterface $passwordHasher, UserProviderInterface $userProvider): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $form = $this->createForm(UpdatePasswordType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                /** @var PasswordAuthenticatedUserInterface $user */
                $user = $this->getUser();
                $hashedPassword = $passwordHasher->hashPassword($user, $form->get('password')->getData());
                $userProvider->upgradePassword($user, $hashedPassword);

                $this->addFlash('success', 'Le mot de passe a été modifié');
            } catch (\Exception $e) {
                $this->logger->error($e->getMessage(), ['exception' => $e]);
                $this->addFlash('error', 'Impossible de modifier le mot de passe');
            }
        }

        return $this->render('@CyvePasswordManager/update-password.html.twig', ['form' => $form->createView()]);
    }
}
