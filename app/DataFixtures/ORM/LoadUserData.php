<?php

namespace AgentPlus\DataFixtures\ORM;

use AgentPlus\Component\Kernel\KernelAwareInterface;
use AgentPlus\Component\Kernel\KernelAwareTrait;
use AgentPlus\Entity\User\User;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadUserData extends AbstractFixture implements KernelAwareInterface, OrderedFixtureInterface
{
    use KernelAwareTrait;

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        foreach (self::getUsers() as $username => $userInfo) {
            $this->createUserViaUserInfo($manager, $username, $userInfo);
        }

        $manager->flush();
    }

    /**
     * Create a user via user info
     *
     * @param ObjectManager $manager
     * @param string        $username
     * @param array         $userInfo
     *
     * @return User
     */
    private function createUserViaUserInfo(ObjectManager $manager, $username, array $userInfo)
    {
        if ($userInfo['type'] == 'reference') {
            // Get user as reference
            return $this->getReference('user:' . $username);
        }

        $user = new User(
            $userInfo['type'],
            $username,
            $userInfo['email'],
            $userInfo['password']
        );

        $this->kernel->getUserPasswordUpdater()->update($user);
        $manager->persist($user);
        $manager->flush();

        $this->setReference('user:' . $username, $user);

        return $user;
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 0;
    }

    /**
     * Get users
     *
     * @return array
     */
    public static function getUsers()
    {
        return [
            'agentplus' => [
                'email' => 'agentplus@domain.com',
                'password' => 'agentplus',
                'type' => User::TYPE_AGENT,
            ],

            'richard' => [
                'email' => 'richard@office.com',
                'password' => 'richard',
                'type' => User::TYPE_PERSONAL,
            ],

            'suzi' => [
                'email' => 'suzi@office.com',
                'password' => 'suzi',
                'type' => User::TYPE_PERSONAL
            ],

            'sergey' => [
                'email' => 'sergey@office.com',
                'password' => 'sergey',
                'type' => User::TYPE_PERSONAL
            ],

            'roma' => [
                'email' => 'roma@office.com',
                'password' => 'roma',
                'type' => User::TYPE_PERSONAL
            ],

            'yulia' => [
                'email' => 'yulia@office.com',
                'password' => 'yulia',
                'type' => User::TYPE_PERSONAL
            ]
        ];
    }
}
