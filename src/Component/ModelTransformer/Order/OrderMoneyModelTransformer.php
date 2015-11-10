<?php

namespace AgentPlus\Component\ModelTransformer\Order;

use AgentPlus\Entity\Order\Money;
use AgentPlus\Model\Order\Money as MoneyModel;
use FiveLab\Component\ModelTransformer\ContextInterface;
use FiveLab\Component\ModelTransformer\Exception\TransformationFailedException;
use FiveLab\Component\ModelTransformer\ModelTransformerInterface;
use FiveLab\Component\Reflection\Reflection;

class OrderMoneyModelTransformer implements ModelTransformerInterface
{
    /**
     * {@inheritDoc}
     */
    public function transform($object, ContextInterface $context)
    {
        if (!$object instanceof Money) {
            throw TransformationFailedException::unexpected($object, Money::class);
        }

        $currency = $object->getCurrency();
        $money = new MoneyModel();

        Reflection::setPropertiesValue($money, [
            'amount' => $object->getAmount(),
            'currency' => $currency
        ]);

        return $money;
    }

    /**
     * {@inheritDoc}
     */
    public function supportsClass($class)
    {
        return is_a($class, Money::class, true);
    }
}
