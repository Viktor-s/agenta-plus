<?php

namespace AgentPlus\Model\Normalizer;

use AgentPlus\Model\Order\Order;
use FiveLab\Component\ModelNormalizer\ContextInterface;
use FiveLab\Component\ModelNormalizer\Exception\DenormalizationFailedException;
use FiveLab\Component\ModelNormalizer\Exception\NormalizationFailedException;
use FiveLab\Component\ModelNormalizer\ModelNormalizerInterface;
use FiveLab\Component\ModelNormalizer\ModelNormalizerManagerAwareInterface;
use FiveLab\Component\ModelNormalizer\ModelNormalizerManagerInterface;

class OrderModelNormalizer implements ModelNormalizerInterface, ModelNormalizerManagerAwareInterface
{
    /**
     * @var ModelNormalizerManagerInterface
     */
    private $modelNormalizer;

    /**
     * {@inheritDoc}
     */
    public function setModelNormalizerManager(ModelNormalizerManagerInterface $normalizerManager)
    {
        $this->modelNormalizer = $normalizerManager;
    }

    /**
     * {@inheritDoc}
     */
    public function normalize($object, ContextInterface $context)
    {
        if (!$object instanceof Order) {
            throw NormalizationFailedException::unexpected($object, Order::class);
        }

        $createdAt = $this->modelNormalizer->normalize($object->getCreatedAt());
        $updatedAt = $this->modelNormalizer->normalize($object->getUpdatedAt());
        $client = $this->modelNormalizer->normalize($object->getClient());
        $creator = $this->modelNormalizer->normalize($object->getCreator());
        $factories = $this->modelNormalizer->normalize($object->getFactories());
        $stage = $this->modelNormalizer->normalize($object->getStage());
        $money = $this->modelNormalizer->normalize($object->getMoney());

        $data = [
            'id' => $object->getId(),
            'createdAt' => $createdAt,
            'updatedAt' => $updatedAt,
            'client' => $client,
            'creator' => $creator,
            'factories' => $factories,
            'stage' => $stage,
            'money' => $money
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
