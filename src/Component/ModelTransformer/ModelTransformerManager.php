<?php

namespace AgentPlus\Component\ModelTransformer;

use Doctrine\ORM\Proxy\Proxy;
use FiveLab\Component\ModelTransformer\ContextInterface;
use FiveLab\Component\ModelTransformer\ModelTransformerManager as BaseModelTransformerManager;

class ModelTransformerManager extends BaseModelTransformerManager
{
    /**
     * {@inheritDoc}
     */
    public function transform($object, ContextInterface $context = null)
    {
        if ($object instanceof Proxy) {
            if (!$object->__isInitialized()) {
                call_user_func($object->__getInitializer(), $object);
            }
        }

        return parent::transform($object);
    }
}
