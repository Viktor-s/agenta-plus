<?php

namespace AgentPlus\DataFixtures\ORM;

use AgentPlus\Entity\Diary\Type;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadDiaryTypeData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        foreach (self::getTypes() as $key => $typeInfo) {
            $typeInfo += [
                'position' => 0,
                'parent' => 0
            ];

            $name = $typeInfo['name'];
            $parent = null;

            if ($typeInfo['parent']) {
                $parent = $this->getReference('diary-type:' . $typeInfo['parent']);
            }

            $type = new Type($name, $parent);
            $type->setPosition($typeInfo['position']);

            $this->setReference('diary-type:' . $key, $type);

            $manager->persist($type);
            $manager->flush();
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 0;
    }

    /**
     * Get diary types
     *
     * @return array
     */
    public static function getTypes()
    {
        return [
            'call' => [
                'name' => 'Call',
            ],

            'call_in' => [
                'name' => 'In',
                'parent' => 'call'
            ],

            'call_out' => [
                'name' => 'Out',
                'parent' => 'call'
            ],

            'meeting' => [
                'name' => 'Meeting'
            ],

            'meeting_in' => [
                'name' => 'In',
                'parent' => 'meeting'
            ],

            'meeting_out' => [
                'name' => 'Out',
                'parent' => 'meeting'
            ],

            'meeting_fair' => [
                'name' => 'Meeting fair'
            ]
        ];
    }
}
