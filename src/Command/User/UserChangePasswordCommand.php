<?php

namespace AgentPlus\Command\User;

use AgentPlus\Repository\UserRepository;
use AgentPlus\Security\UserPasswordUpdater;
use FiveLab\Component\Transactional\TransactionalInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command for change user password
 */
class UserChangePasswordCommand extends Command
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var UserPasswordUpdater
     */
    private $passwordUpdater;

    /**
     * Construct
     *
     * @param UserPasswordUpdater    $passwordUpdater
     * @param UserRepository         $userRepository
     * @param TransactionalInterface $transactional
     */
    public function __construct(
        UserPasswordUpdater $passwordUpdater,
        UserRepository $userRepository,
        TransactionalInterface $transactional
    ) {
        $this->passwordUpdater = $passwordUpdater;
        $this->userRepository = $userRepository;
        $this->transactional = $transactional;

        parent::__construct();
    }

    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this
            ->setName('user:change-password')
            ->setDescription('Change password for user')
            ->addArgument('username', InputArgument::REQUIRED, 'The username or email of user for change password')
            ->addArgument('password', InputArgument::REQUIRED, 'The new password of user');
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $username = $input->getArgument('username');
        $password = $input->getArgument('password');

        $user = $this->userRepository->findByUsernameOrEmail($username);

        if (!$user) {
            $output->writeln(sprintf(
                '<error>Not found user with username "%s".</error>',
                $username
            ));

            return 1;
        }

        $user->setPlainPassword($password);
        $this->transactional->execute(function () use ($user) {
            $this->passwordUpdater->update($user);
        });

        $output->writeln(sprintf(
            'Success update password for user <info>%s</info>.',
            $username
        ));

        return 0;
    }
}
