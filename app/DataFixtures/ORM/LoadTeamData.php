<?php

namespace AgentPlus\DataFixtures\ORM;

use AgentPlus\Entity\User\Team;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadTeamData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        foreach (self::getTeams() as $key => $teamInfo) {
            $team = new Team($teamInfo['name']);

            foreach ($teamInfo['members'] as $member) {
                /** @var \AgentPlus\Entity\User\User $member */
                $member = $this->getReference('user:' . $member);
                $team->addUser($member);
            }

            $this->addReference('team:' . $key, $team);
        }

        $manager->flush();
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 1;
    }

    /**
     * Get teams
     *
     * @return array
     */
    public static function getTeams()
    {
        return [
            'office_in_kyiv' => [
                'name' => 'Office in Kyiv',
                'members' => ['richard', 'suzi', 'sergey']
            ],

            'office_in_lviv' => [
                'name' => 'Office in Lviv',
                'members' => ['roma', 'yulia']
            ],

            'office_in_lutsk' => [
                'name' => 'Office in Lutsk',
                'members' => []
            ]
        ];
    }
}
