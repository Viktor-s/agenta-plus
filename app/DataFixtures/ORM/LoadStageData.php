<?php

namespace AgentPlus\DataFixtures\ORM;

use AgentPlus\Entity\Order\Stage;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadStageData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $position = 0;

        foreach (self::getStages() as $key => $label) {
            $stage = new Stage($label, $position++);
            $manager->persist($stage);
            $this->setReference('stage:' . $key, $stage);
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
     * Get stages
     *
     * @return array
     */
    public static function getStages()
    {
        return [
            'choose_product' => 'Choose product',
            'commercial_proposal' => 'Commercial proposal',
            'request_to_factory' => 'Request to factory',
            'proforma_to_retail' => 'Send proforma to retail',
            'accept_proforma' => 'Accept proforma',
            'copy_of_payment' => 'Copy of payment',
            'balance_order' => 'Balance order',
            'shipping_documents' => 'Shipping documents'
        ];
    }
}
