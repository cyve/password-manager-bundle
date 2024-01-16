<?php

namespace Cyve\PasswordManagerBundle\Controller;

use Cyve\PasswordManagerBundle\Entity\EmailAwareUserInterface;
use Cyve\PasswordManagerBundle\Form\RequestLoginLinkType;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Symfony\Bridge\Twig\Mime\NotificationEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\LoginLink\LoginLinkHandlerInterface;

class RequestLoginLinkController extends AbstractController implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    #[Route('/password/request-login-link', name: 'cyve_password_manager_request_login_link', methods: ['GET', 'POST'])]
    public function __invoke(
        Request $request,
        UserProviderInterface $userProvider,
        MailerInterface $mailer,
        LoginLinkHandlerInterface $loginLinkHandler
    ): Response {
        $form = $this->createForm(RequestLoginLinkType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                /** @var EmailAwareUserInterface $user */
                $user = $userProvider->loadUserByIdentifier($form->get('email')->getData());
                $loginLinkDetails = $loginLinkHandler->createLoginLink($user);
                $loginLink = $loginLinkDetails->getUrl().'&_target_path='.$this->generateUrl('cyve_password_manager_update_password');
                $duration = floor(($loginLinkDetails->getExpiresAt()->getTimestamp() - time()) / 60);

                $email = NotificationEmail::asPublicEmail()
                    ->to($user->getEmail())
                    ->subject('Réinitialisation de votre mot de passe')
                    ->content(sprintf('Cliquez sur le bouton ci-dessous pour vous connecter et réinitialiser votre mot de passe. Ce lien expirera dans %d minutes.', $duration))
                    ->action('Connexion', $loginLink)
                ;
                $mailer->send($email);

                return $this->render('@CyvePasswordManager/request-login-link-confirmation.html.twig', ['form' => $form->createView(), 'duration' => $duration]);
            } catch (UserNotFoundException $e) {
                $this->addFlash('danger', sprintf('L\'utilisateur "%s" n\'existe pas', $e->getUserIdentifier()));
            } catch (\Exception $e) {
                $this->logger->error($e->getMessage(), ['exception' => $e]);
                $this->addFlash('danger', 'Impossible de réinitialiser le mot de passe');
            }
        }

        return $this->render('@CyvePasswordManager/request-login-link.html.twig', ['form' => $form->createView()]);
    }
}
