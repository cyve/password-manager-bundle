<?php

namespace Cyve\PasswordManagerBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * @group functionnal
 */
class ResetPasswordTest extends WebTestCase
{
    public function test()
    {
        $client = self::createClient();
        $userProvider = self::getContainer()->get('security.user.provider.concrete.app_user_provider');
        $loginLinkHandler = self::getContainer()->get('security.authenticator.login_link_handler.main');
        $user = $userProvider->loadUserByIdentifier('lorem@mail.com');
        $loginLinkUrl = $loginLinkHandler->createLoginLink($user)->getUrl().'&_target_path=/password/update';

        $this->assertStringStartsWith('http://localhost/password/reset?user=lorem@mail.com', $loginLinkUrl);

        $client->request('GET', $loginLinkUrl);
        $this->assertResponseRedirects('http://localhost/password/update');

        $client->followRedirect();
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Modifier le mot de passe');
    }

    public function testWithInvalidToken()
    {
        $client = self::createClient();
        $client->request('GET', '/password/reset?user=admin@mail.com&expires=0&hash=foo');

        $this->assertResponseRedirects('http://localhost/login');
    }
}
