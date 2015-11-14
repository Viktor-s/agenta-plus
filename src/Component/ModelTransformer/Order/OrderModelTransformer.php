<?php

namespace AgentPlus\Component\ModelTransformer\Order;

use AgentPlus\Entity\Order\Order;
use AgentPlus\Model\Order\Order as OrderModel;
use FiveLab\Component\ModelTransformer\ContextInterface;
use FiveLab\Component\ModelTransformer\Exception\TransformationFailedException;
use FiveLab\Component\ModelTransformer\ModelTransformerInterface;
use FiveLab\Component\ModelTransformer\ModelTransformerManagerAwareInterface;
use FiveLab\Component\ModelTransformer\ModelTransformerManagerInterface;
use FiveLab\Component\Reflection\Reflection;

class OrderModelTransformer implements ModelTransformerInterface, ModelTransformerManagerAwareInterface
{
    /**
     * @var ModelTransformerManagerInterface
     */
    private $modelTransformer;

    /**
     * {@inheritDoc}
     */
    public function setModelTransformerManager(ModelTransformerManagerInterface $manager)
    {
        $this->modelTransformer = $manager;
    }

    /**
     * {@inheritDoc}
     */
    public function transform($object, ContextInterface $context)
    {
        if (!$object instanceof Order) {
            throw TransformationFailedException::unexpected($object, Order::class);
        }

        $order = new OrderModel();

        Reflection::setPropertiesValue($order, [
            'id' => $object->getId(),
            'creator' => $this->modelTransformer->transform($object->getCreator()),
            'client' => $this->modelTransformer->transform($object->getClient()),
            'createdAt' => $object->getCreatedAt(),
            'updatedAt' => $object->getUpdatedAt(),
            'factories' => $this->modelTransformer->transform($object->getFactories()),
            'money' => $this->modelTransformer->transform($object->getMoney()),
            'stage' => $this->modelTransformer->transform($object->getStage())
        ]);

        return $order;
    }

    /**
     * {@inheritDoc}
     */
    public function supportsClass($class)
    {
        return is_a($class, Order::class, true);
    }
}
