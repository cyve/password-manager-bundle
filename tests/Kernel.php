<?php

namespace Cyve\PasswordManagerBundle\Tests;

use Cyve\PasswordManagerBundle\CyvePasswordManagerBundle;
use Cyve\PasswordManagerBundle\Security\ResetPasswordSuccessHandler;
use Cyve\PasswordManagerBundle\Tests\Mock\InMemoryUserProvider;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Bundle\SecurityBundle\SecurityBundle;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Twig\Extra\TwigExtraBundle\TwigExtraBundle;
use Twig\Loader\ArrayLoader;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    public function registerBundles(): iterable
    {
        return [
            new FrameworkBundle(),
            new TwigBundle(),
            new TwigExtraBundle(),
            new SecurityBundle(),
            new CyvePasswordManagerBundle(),
        ];
    }

    protected function configureContainer(ContainerConfigurator $container): void
    {
        $container->extension('framework', [
            'test' => true,
            'secret' => uniqid(),
            'session' => [
                'storage_factory_id' => 'session.storage.factory.mock_file',
            ],
            'mailer' => [
                'dsn' => 'null://null',
                'envelope' => [
                    'sender' => 'Webmaster <webmaster@localhost>',
                ],
            ],
        ]);

        $container->extension('twig', [
            'form_themes' => ['bootstrap_5_layout.html.twig'],
        ]);

        $container->extension('security', [
            'enable_authenticator_manager' => true,
            'password_hashers' => [
                PasswordAuthenticatedUserInterface::class => 'plaintext',
            ],
            'providers' => [
                'app_user_provider' => [
                    'id' => InMemoryUserProvider::class,
                ],
            ],
            'firewalls' => [
                'main' => [
                    'provider' => 'app_user_provider',
                    'login_link' => [
                        'check_route' => 'cyve_password_manager_reset_password',
                        'signature_properties' => ['id'],
                        'success_handler' => ResetPasswordSuccessHandler::class,
                    ],
                ],
            ],
        ]);

        $container->import(__DIR__.'./../src/Resources/config/services.yaml');

        $container->services()->set(InMemoryUserProvider::class);
        $container->services()->remove('twig.loader.filesystem');
        $container->services()->set('twig.loader.array_loader', ArrayLoader::class)->args([
            ['base.html.twig' => '<html><head><title>{%% block title %%}{%% endblock %%}</title></head><body>{%% block body %%}{%% endblock %%}</body></html>'],
        ])->tag('twig.loader');
    }

    protected function configureRoutes(RoutingConfigurator $routes): void
    {
        $routes->import(__DIR__.'./../src/Resources/config/routing.yaml');
    }
}
