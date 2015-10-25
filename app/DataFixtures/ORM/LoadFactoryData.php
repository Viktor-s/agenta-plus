<?php

namespace AgentPlus\DataFixtures\ORM;

use AgentPlus\Entity\Factory\Factory;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadFactoryData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        foreach (self::getFactories() as $key => $factoryInfo) {
            $factory = new Factory($factoryInfo['name']);
            $manager->persist($factory);
            $this->setReference('factory:' . $key, $factory);
        }

        $manager->flush();
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 0;
    }

    /**
     * Get factories
     *
     * @return array
     */
    public static function getFactories()
    {
        return [
            'selva' => [
                'name' => 'Selva'
            ],

            'antonello_italia' => [
                'name' => 'Antonello Italia'
            ]
        ];
    }
}
