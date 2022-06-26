<?php

namespace Cyve\PasswordManagerBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * @group functionnal
 */
class UpdatePasswordTest extends WebTestCase
{
    public function test()
    {
        $client = self::createClient();
        $container = self::getContainer();
        $userProvider = $container->get('security.user.provider.concrete.app_user_provider');
        $user = $userProvider->loadUserByIdentifier('lorem@mail.com');
        $client->loginUser($user);

        $client->request('GET', '/password/update');

        $this->assertResponseIsSuccessful();

        $client->submitForm('Enregistrer', [
            'update_password[password]' => 'lorem2',
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('.alert.alert-success', 'Le mot de passe a été modifié');

        $userProvider = $container->get('security.user.provider.concrete.app_user_provider');
        $user = $userProvider->loadUserByIdentifier('lorem@mail.com');
        $this->assertEquals('lorem2', $user->getPassword());
    }

    public function testNotAuthenticated()
    {
        self::createClient()->request('GET', '/password/update');

        $this->assertResponseStatusCodeSame(401);
    }
}
