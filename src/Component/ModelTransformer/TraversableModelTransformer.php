<?php

namespace AgentPlus\Component\ModelTransformer;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\PersistentCollection;
use FiveLab\Component\ModelTransformer\Transformer\TraversableModelTransformer as BaseTraversableModelTransformer;

class TraversableModelTransformer extends BaseTraversableModelTransformer
{
    /**
     * {@inheritDoc}
     */
    protected function createCollection($collection)
    {
        if ($collection instanceof PersistentCollection) {
            return new ArrayCollection($collection->toArray());
        }

        return parent::createCollection($collection);
    }
}
