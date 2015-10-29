<?php

namespace AgentPlus\Command\User;

use AgentPlus\Entity\User\User;
use AgentPlus\Repository\UserRepository;
use AgentPlus\Security\UserPasswordUpdater;
use FiveLab\Component\Transactional\TransactionalInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command for create user
 */
class UserCreateCommand extends Command
{
    /**
     * @var UserPasswordUpdater
     */
    private $passwordUpdater;

    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var TransactionalInterface
     */
    private $transactional;

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
            ->setName('user:create')
            ->setDescription('Create a new user')
            ->addArgument('username', InputArgument::REQUIRED, 'The username of user')
            ->addArgument('email', InputArgument::REQUIRED, 'The email of user')
            ->addArgument('password', InputArgument::REQUIRED, 'The password of user');
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $username = $input->getArgument('username');
        $password = $input->getArgument('password');
        $email = $input->getArgument('email');

        // Try load user by username
        if ($this->userRepository->findByUsername($username)) {
            $output->writeln(sprintf(
                '<error>The user with username "%s" already exist.</error>',
                $username
            ));

            return 1;
        }

        // Try load user by email
        if ($this->userRepository->findByEmail($email)) {
            $output->writeln(sprintf(
                '<error>The user with email "%s" already exist.</error>',
                $email
            ));

            return 1;
        }

        $user = new User($username, $email, $password);

        $this->passwordUpdater->update($user);

        $this->transactional->execute(function () use ($user) {
            $this->userRepository->add($user);
        });

        $output->writeln(sprintf(
            'Success create user with username <info>%s</info>.',
            $user->getUsername()
        ));

        return 0;
    }

    /**
     * {@inheritDoc}
     */
    protected function interact(InputInterface $input, OutputInterface $output)
    {
        if (!$input->getArgument('username')) {
            $username = $this->getHelper('dialog')->askAndValidate(
                $output,
                'Please choose a username:',
                function ($username) {
                    if (empty($username)) {
                        throw new \Exception('Username can not be empty');
                    }

                    return $username;
                }
            );

            $input->setArgument('username', $username);
        }

        if (!$input->getArgument('email')) {
            $email = $this->getHelper('dialog')->askAndValidate(
                $output,
                'Please choose a email:',
                function ($email) {
                    if (empty($email)) {
                        throw new \Exception('Email can not be empty');
                    }

                    return $email;
                }
            );

            $input->setArgument('email', $email);
        }

        if (!$input->getArgument('password')) {
            $password = $this->getHelper('dialog')->askAndValidate(
                $output,
                'Please choose a password:',
                function ($password) {
                    if (empty($password)) {
                        throw new \Exception('Password can not be empty');
                    }

                    return $password;
                }
            );

            $input->setArgument('password', $password);
        }
    }
}
