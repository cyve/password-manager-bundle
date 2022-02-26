<?php

namespace Cyve\PasswordManagerBundle\Tests\Command;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @group functionnal
 */
class ResetPasswordCommandTest extends KernelTestCase
{
    protected function setUp(): void
    {
        $application = new Application(self::bootKernel());
        $this->commandTester = new CommandTester($application->find('cyve:password:reset'));
    }

    public function test()
    {
        $this->commandTester->execute(['username' => 'lorem@mail.com', 'password' => 'lorem2']);

        $this->assertEquals(0, $this->commandTester->getStatusCode());
        $this->assertStringContainsString('[OK] Password updated successfully', $this->commandTester->getDisplay());

        $userProvider = static::getContainer()->get('security.user.provider.concrete.app_user_provider');
        $user = $userProvider->loadUserByIdentifier('lorem@mail.com');
        $this->assertEquals('lorem2', $user->getPassword());
    }

    public function testUserNotFound()
    {
        $this->commandTester->execute(['username' => 'ipsum@mail.com', 'password' => 'lorem2']);

        $this->assertEquals(1, $this->commandTester->getStatusCode());
        $this->assertStringContainsString('[ERROR] User "ipsum@mail.com" not found', $this->commandTester->getDisplay());
    }
}
