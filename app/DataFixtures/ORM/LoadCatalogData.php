<?php

namespace AgentPlus\DataFixtures\ORM;

use AgentPlus\Entity\Catalog\Catalog;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadCatalogData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        foreach (self::getCatalogs() as $key => $catalogInfo) {
            $factories = new ArrayCollection();

            foreach ($catalogInfo['factories'] as $factoryReference) {
                $factories->add($this->getReference('factory:' . $factoryReference));
            }

            /** @var \AgentPlus\Entity\User\User $creator */
            $creator = $this->getReference('user:' . $catalogInfo['creator']);

            $catalog = new Catalog($creator, $catalogInfo['name']);
            $catalog->replaceFactories($factories);

            $manager->persist($catalog);
            $this->setReference('catalog:' . $key, $catalog);
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
     * Get catalogs
     *
     * @return array
     */
    public static function getCatalogs()
    {
        return [
            'premium' => [
                'name' => 'Premium catalog',
                'creator' => 'agentplus',
                'factories' => ['selva', 'antonello_italia']
            ],

            'selva_sales' => [
                'name' => 'Selva sales',
                'creator' => 'richard',
                'factories' => ['selva']
            ],

            'selva_gold' => [
                'name' => 'Gold of Selva products',
                'creator' => 'suzi',
                'factories' => ['selva']
            ],

            'antonello_italia_summer_collection' => [
                'name' => 'Summer collection of Antonello Italia',
                'creator' => 'agentplus',
                'factories' => ['antonello_italia']
            ]
        ];
    }
}
