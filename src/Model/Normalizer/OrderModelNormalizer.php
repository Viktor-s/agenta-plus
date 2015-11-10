<?php

namespace AgentPlus\Model\Normalizer;

use AgentPlus\Model\Order\Order;
use FiveLab\Component\ModelNormalizer\ContextInterface;
use FiveLab\Component\ModelNormalizer\Exception\DenormalizationFailedException;
use FiveLab\Component\ModelNormalizer\Exception\NormalizationFailedException;
use FiveLab\Component\ModelNormalizer\ModelNormalizerInterface;

class OrderModelNormalizer implements ModelNormalizerInterface
{
    /**
     * {@inheritDoc}
     */
    public function normalize($object, ContextInterface $context)
    {
        if (!$object instanceof Order) {
            throw NormalizationFailedException::unexpected($object, Order::class);
        }

        $data = [
            'id' => $object->getId()
        ];

        return $data;
    }

    /**
     * {@inheritDoc}
     */
    public function denormalize($data, ContextInterface $context)
    {
        throw new DenormalizationFailedException('Unsupported');
    }

    /**
     * {@inheritDoc}
     */
    public function supportsClass($class)
    {
        return is_a($class, Order::class, true);
    }
}
