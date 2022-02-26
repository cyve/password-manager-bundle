<?php

namespace Cyve\PasswordManagerBundle\Tests\Controller;

use Symfony\Bridge\Twig\Mime\NotificationEmail;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

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
        $this->assertSelectorNotExists('.alert');
        $this->assertSelectorTextContains('p', 'Ce lien expirera dans 10 minutes');

        $this->assertEmailCount(1);
        $message = $this->getMailerMessage();
        $this->assertInstanceOf(NotificationEmail::class, $message);
        $this->assertEmailHeaderSame($message, 'To', 'lorem@mail.com');
        $this->assertEmailHeaderSame($message, 'Sender', 'webmaster@localhost');
        $this->assertEmailHeaderSame($message, 'Subject', 'Réinitialisation de votre mot de passe');
        $this->assertEmailTextBodyContains($message, 'Ce lien expirera dans 10 minutes');
        $messageContext = $message->getContext();
        $this->assertStringStartsWith('http://localhost/password/reset?user=lorem@mail.com', $messageContext['action_url']);
        $this->assertStringStartsWith('Connexion', $messageContext['action_text']);
    }

    public function testMissingEmail()
    {
        $client = self::createClient();
        $client->request('GET', '/password/request-login-link');
        $client->submitForm('Réinitialiser', [
            'request_login_link[email]' => '',
        ]);

        $this->assertSelectorExists('#request_login_link_email.is-invalid');
        $this->assertSelectorTextContains('#request_login_link_email + .invalid-feedback', 'Ce champ ne doit pas être vide');
    }

    public function testInvalidEmail()
    {
        $client = self::createClient();
        $client->request('GET', '/password/request-login-link');
        $client->submitForm('Réinitialiser', [
            'request_login_link[email]' => 'not email',
        ]);

        $this->assertSelectorExists('#request_login_link_email.is-invalid');
        $this->assertSelectorTextContains('#request_login_link_email + .invalid-feedback', 'Ce champ doit être une adresse email valide');
    }

    public function testUnknownEmail()
    {
        $client = self::createClient();
        $client->request('GET', '/password/request-login-link');
        $client->submitForm('Réinitialiser', [
            'request_login_link[email]' => 'unknown@mail.com',
        ]);

        $this->assertSelectorTextContains('.alert', 'L\'utilisateur "unknown@mail.com" n\'existe pas');
    }
}
