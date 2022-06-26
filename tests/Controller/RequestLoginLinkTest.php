<?php

namespace Cyve\PasswordManagerBundle\Tests\Controller;

use Symfony\Bridge\Twig\Mime\NotificationEmail;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Mailer\MailerInterface;

/**
 * @group functionnal
 */
class RequestLoginLinkTest extends WebTestCase
{
    public function test()
    {
        $client = self::createClient();
        $client->request('GET', '/password/request-login-link');

        $this->assertResponseIsSuccessful();

        $client->submitForm('Réinitialiser', [
            'request_login_link[email]' => 'lorem@mail.com',
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertSelectorNotExists('.alert.alert-error');
        $this->assertSelectorTextContains('p', 'Ce lien expirera dans 10 minutes');

        $mailer = $client->getContainer()->get(MailerInterface::class);
        $messages = $mailer->getMessages();
        $this->assertCount(1, $messages);
        $message = $messages[0];
        $this->assertInstanceOf(NotificationEmail::class, $message);

        $this->assertEquals('Réinitialisation de votre mot de passe', $message->getSubject());
        $this->assertEquals('lorem@mail.com', $message->getTo()[0]->toString());
        $messageContext = $message->getContext();
        $this->assertStringContainsString('Ce lien expirera dans 10 minutes', $messageContext['content']);
        $this->assertStringStartsWith('http://localhost/password/reset?user=lorem@mail.com', $messageContext['action_url']);
        $this->assertStringStartsWith('Connexion', $messageContext['action_text']);

    }

    public function testSubmitWithoutEmail()
    {
        $client = self::createClient();
        $client->request('GET', '/password/request-login-link');

        $this->assertResponseIsSuccessful();

        $client->submitForm('Réinitialiser', [
            'request_login_link[email]' => '',
        ]);

        $this->assertSelectorExists('#request_login_link_email.is-invalid');
        $this->assertSelectorTextContains('#request_login_link_email + .invalid-feedback', 'Ce champ ne doit pas être vide');
    }

    public function testSubmitWithInvalidEmail()
    {
        $client = self::createClient();
        $client->request('GET', '/password/request-login-link');

        $this->assertResponseIsSuccessful();

        $client->submitForm('Réinitialiser', [
            'request_login_link[email]' => 'not email',
        ]);

        $this->assertSelectorExists('#request_login_link_email.is-invalid');
        $this->assertSelectorTextContains('#request_login_link_email + .invalid-feedback', 'Ce champ doit être une adresse email valide');
    }

    public function testSubmitWithUnknownEmail()
    {
        $client = self::createClient();
        $client->request('GET', '/password/request-login-link');

        $this->assertResponseIsSuccessful();

        $client->submitForm('Réinitialiser', [
            'request_login_link[email]' => 'unknown@mail.com',
        ]);

        $this->assertSelectorTextContains('.alert.alert-danger', 'L\'utilisateur "unknown@mail.com" n\'existe pas');
    }
}
