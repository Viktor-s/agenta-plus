<?php

namespace AgentPlus\DataFixtures\ORM;

use AgentPlus\Entity\Currency;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadCurrencyData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        foreach (self::getCurrencies() as $currencyCode => $currencyInfo) {
            $currency = new Currency($currencyCode, $currencyInfo['position']);
            $manager->persist($currency);
            $this->setReference('currency:' . $currencyCode, $currency);
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
     * Get currencies
     *
     * @return array
     */
    public static function getCurrencies()
    {
        return [
            'USD' => [
                'position' => 0
            ],

            'EUR' => [
                'position' => 1,
            ],

            'UAH' => [
                'position' => 2
            ]
        ];
    }
}
