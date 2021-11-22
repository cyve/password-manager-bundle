<?php

namespace Cyve\PasswordManagerBundle\Tests\Controller;

use Hautelook\AliceBundle\PhpUnit\RecreateDatabaseTrait;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UpdatePasswordTest extends WebTestCase
{
    use RecreateDatabaseTrait;

    public function test()
    {
        $client = self::createClient();
        $userProvider = static::getContainer()->get('security.user.provider.concrete.app_user_provider');
        $user = $userProvider->loadUserByIdentifier('lorem@mail.com');
        $client->loginUser($user);

        $client->request('GET', '/password/update');

        $this->assertResponseIsSuccessful();

        $client->submitForm('Enregistrer', [
            'update_password[password][first]' => 'lorem2',
            'update_password[password][second]' => 'lorem2',
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('.alert.alert-success', 'Le mot de passe a été modifié');

        $user = $userProvider->loadUserByIdentifier('lorem@mail.com');
        $this->assertEquals('lorem2', $user->getPassword());
    }

    public function testInvalidData()
    {
        $client = self::createClient();
        $userProvider = static::getContainer()->get('security.user.provider.concrete.app_user_provider');
        $user = $userProvider->loadUserByIdentifier('lorem@mail.com');
        $client->loginUser($user);

        $client->request('GET', '/password/update');
        $client->submitForm('Enregistrer', [
            'update_password[password][first]' => 'lorem',
            'update_password[password][second]' => 'lorem2',
        ]);

        $this->assertSelectorTextContains('[for="update_password_password_first"] + *', 'Les valeurs ne correspondent pas');
    }

    public function testNotAuthenticated()
    {
        self::createClient()->request('GET', '/password/update');

        $this->assertResponseRedirects('http://localhost/login');
    }
}
