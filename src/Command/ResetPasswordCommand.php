<?php

namespace Cyve\PasswordManagerBundle\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

#[AsCommand(name: 'cyve:password:reset')]
class ResetPasswordCommand extends Command
{
    private UserProviderInterface $userProvider;
    private UserPasswordHasherInterface $userpasswordHasher;

    public function __construct(UserProviderInterface $userProvider, UserPasswordHasherInterface $userpasswordHasher)
    {
        if (!$userProvider instanceof PasswordUpgraderInterface) {
            throw new InvalidArgumentException(sprintf('Argument "%s" must implement interface "%s".', '$userProvider', UserPasswordHasherInterface::class));
        }

        $this->userProvider = $userProvider;
        $this->userpasswordHasher = $userpasswordHasher;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Reset user password')
            ->addArgument('username', InputArgument::REQUIRED, 'Username')
            ->addArgument('password', InputArgument::REQUIRED, 'New password')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $username = $input->getArgument('username');
        $password = $input->getArgument('password');

        try {
            $user = $this->userProvider->loadUserByIdentifier($username);
            $hash = $this->userpasswordHasher->hashPassword($user, $password);
            $this->userProvider->upgradePassword($user, $hash);

            $io->success('Password updated successfully');

            return Command::SUCCESS;
        } catch (UserNotFoundException $e) {
            $io->error(sprintf('User "%s" not found', $username));

            return Command::FAILURE;
        } catch (\Exception $e) {
            $io->error($e->getMessage());

            return Command::FAILURE;
        }
    }
}
