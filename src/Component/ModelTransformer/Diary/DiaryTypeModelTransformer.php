<?php

namespace AgentPlus\Component\ModelTransformer\Diary;

use AgentPlus\Entity\Diary\Type;
use AgentPlus\Model\Diary\Type as TypeModel;
use FiveLab\Component\ModelTransformer\Context;
use FiveLab\Component\ModelTransformer\ContextInterface;
use FiveLab\Component\ModelTransformer\Exception\TransformationFailedException;
use FiveLab\Component\ModelTransformer\ModelTransformerInterface;
use FiveLab\Component\ModelTransformer\ModelTransformerManagerAwareInterface;
use FiveLab\Component\ModelTransformer\ModelTransformerManagerInterface;
use FiveLab\Component\Reflection\Reflection;

class DiaryTypeModelTransformer implements ModelTransformerInterface, ModelTransformerManagerAwareInterface
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
        if (!$object instanceof Type) {
            throw TransformationFailedException::unexpected($object, Type::class);
        }

        $type = new TypeModel();

        Reflection::setPropertiesValue($type, [
            'id' => $object->getId(),
            'name' => $object->getName(),
            'position' => $object->getPosition(),
            'level' => $object->getRootLevel(),
            'fullName' => $object->getFullName()
        ]);

        if ($object->getParent()) {
            $parentId = $object->getParent()->getId();
            Reflection::setPropertyValue($type, 'parentId', $parentId);
        }

        if ($context->getAttribute('with_child')) {
            $transformContext = new Context();
            $transformContext->setAttribute('child_context', $context);

            $child = $this->modelTransformer->transform($object->getChild(), $transformContext);
            Reflection::setPropertyValue($type, 'child', $child);
        }

        return $type;
    }

    /**
     * {@inheritDoc}
     */
    public function supportsClass($class)
    {
        return is_a($class, Type::class, true);
    }
}
