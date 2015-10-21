<?php

namespace AgentPlus\Component\ModelTransformer;

use AgentPlus\Model\Collection;
use AgentPlus\Model\Pagination\DefaultPagination as ModelDefaultPagination;
use FiveLab\Component\ModelTransformer\ContextInterface;
use FiveLab\Component\ModelTransformer\Exception\TransformationFailedException;
use FiveLab\Component\ModelTransformer\ModelTransformerInterface;
use FiveLab\Component\ModelTransformer\ModelTransformerManagerAwareInterface;
use FiveLab\Component\ModelTransformer\ModelTransformerManagerInterface;
use FiveLab\Component\Pagination\Doctrine\ORM\DefaultPagination;
use FiveLab\Component\Reflection\Reflection;

class DefaultORMPaginationModelTransformer implements ModelTransformerInterface, ModelTransformerManagerAwareInterface
{
    /**
     * @var ModelTransformerManagerInterface
     */
    private $transformerManager;

    /**
     * {@inheritDoc}
     */
    public function setModelTransformerManager(ModelTransformerManagerInterface $manager)
    {
        $this->transformerManager = $manager;
    }

    /**
     * {@inheritDoc}
     */
    public function transform($object, ContextInterface $context)
    {
        if (!$object instanceof DefaultPagination) {
            throw TransformationFailedException::unexpected($object, DefaultPagination::class);
        }

        $model = new ModelDefaultPagination();
        $storage = iterator_to_array($object);
        $storage = array_map(function ($object) {
            return $this->transformerManager->transform($object);
        }, $storage);

        $storage = new Collection($storage);

        Reflection::setPropertiesValue($model, [
            'page' => $object->getPage(),
            'limit' => $object->getLimit(),
            'totalItems' => $object->getFullCount(),
            'storage' => $storage
        ]);

        return $model;
    }

    /**
     * {@inheritDoc}
     */
    public function supportsClass($class)
    {
        return is_a($class, DefaultPagination::class, true);
    }
}
