<?php

namespace Cyve\PasswordManagerBundle\Tests\Controller;

use Symfony\Bridge\Twig\Mime\NotificationEmail;
use Symfony\Bundle\FrameworkBundle\Test\MailerAssertionsTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * @group functionnal
 */
class RequestLoginLinkTest extends WebTestCase
{
    use MailerAssertionsTrait;

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

        $this->assertEmailCount(1);

        $email = $this->getMailerMessage();
        $this->assertInstanceOf(NotificationEmail::class, $email);
        $this->assertEmailSubjectContains($email, 'Réinitialisation de votre mot de passe');
        $this->assertEmailAddressContains($email, 'To', 'lorem@mail.com');
        $this->assertEmailTextBodyContains($email, 'Ce lien expirera dans 10 minutes');
        $this->assertEmailHtmlBodyContains($email, 'Ce lien expirera dans 10 minutes');

        $context = $email->getContext();
        $this->assertStringStartsWith('http://localhost/password/reset?user=lorem@mail.com', $context['action_url']);
        $this->assertStringStartsWith('Connexion', $context['action_text']);
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
