<?php

namespace AgentPlus\DataFixtures\ORM;

use AgentPlus\Component\Kernel\KernelAwareInterface;
use AgentPlus\Component\Kernel\KernelAwareTrait;
use AgentPlus\Entity\Team;
use AgentPlus\Entity\User;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;

class LoadUserAndTeamData extends AbstractFixture implements KernelAwareInterface
{
    use KernelAwareTrait;

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        foreach (self::getUsers() as $username => $userInfo) {
            $user = $this->createUserViaUserInfo($manager, $username, $userInfo);

            if ($userInfo['owner_teams']) {
                // The user have a teams.
                foreach ($userInfo['owner_teams'] as $teamName => $teamUsers) {
                    $team = $this->createTeamViaName($manager, $user, $teamName);

                    foreach ($teamUsers as $teamUsername => $teamUserInfo) {
                        $teamUser = $this->createUserViaUserInfo($manager, $teamUsername, $teamUserInfo);
                        $team->addUser($teamUser);
                    }
                }
            }
        }

        $manager->flush();
    }

    /**
     * Create team via name
     *
     * @param ObjectManager $manager
     * @param User          $owner
     * @param string        $teamName
     *
     * @return Team
     */
    private function createTeamViaName(ObjectManager $manager, User $owner, $teamName)
    {
        $team = new Team($owner, $teamName);
        $manager->persist($team);
        $manager->flush();

        $this->setReference('team:' . $teamName, $team);

        return $team;
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
                'owner_teams' => [
                    'Office in Kyiv' => [
                        'richard' => [
                            'email' => 'richard@office.com',
                            'password' => 'richard',
                            'type' => User::TYPE_PERSONAL
                        ],

                        'suzi' => [
                            'email' => 'suzi@office.com',
                            'password' => 'suzi',
                            'type' => User::TYPE_PERSONAL
                        ]
                    ],

                    'Office in Lviv' => [
                        'richard' => [
                            'type' => 'reference'
                        ],

                        'sergey' => [
                            'email' => 'sergey@office.com',
                            'password' => 'sergey',
                            'type' => User::TYPE_PERSONAL
                        ]
                    ],

                    'Office in Lutsk' => []
                ]
            ]
        ];
    }
}
