<?php

namespace Cyve\PasswordManagerBundle\Tests\Controller;

use Hautelook\AliceBundle\PhpUnit\RecreateDatabaseTrait;
use Symfony\Bridge\Twig\Mime\NotificationEmail;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RequestLoginLinkTest extends WebTestCase
{
    use RecreateDatabaseTrait;

    public function test()
    {
        $client = self::createClient();
        $client->request('GET', '/password/request-login-link');

        $this->assertResponseIsSuccessful();

        $client->submitForm('Réinitialiser', [
            'request_login_link[username]' => 'lorem@mail.com',
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertSelectorNotExists('.alert.alert-error');
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

    public function testInvalidData()
    {
        $client = self::createClient();
        $client->request('GET', '/password/request-login-link');
        $client->submitForm('Réinitialiser', [
            'request_login_link[username]' => '',
        ]);

        $this->assertSelectorTextContains('[for="request_login_link_username"] + *', 'Ce champ ne doit pas être vide');
    }

    public function testUnknownUsername()
    {
        $client = self::createClient();
        $client->request('GET', '/password/request-login-link');
        $client->submitForm('Réinitialiser', [
            'request_login_link[username]' => 'unknown',
        ]);

        $this->assertSelectorTextContains('.alert.alert-error', 'L\'utilisateur "unknown" n\'existe pas');
    }
}
