<?php

namespace AgentPlus\Component\ModelTransformer\Diary;

use AgentPlus\Entity\Diary\Diary;
use AgentPlus\Model\Diary\Diary as DiaryModel;;
use FiveLab\Component\ModelTransformer\ContextInterface;
use FiveLab\Component\ModelTransformer\Exception\TransformationFailedException;
use FiveLab\Component\ModelTransformer\ModelTransformerInterface;
use FiveLab\Component\ModelTransformer\ModelTransformerManagerAwareInterface;
use FiveLab\Component\ModelTransformer\ModelTransformerManagerInterface;
use FiveLab\Component\Reflection\Reflection;

class DiaryModelTransformer implements ModelTransformerInterface, ModelTransformerManagerAwareInterface
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
        if (!$object instanceof Diary) {
            throw TransformationFailedException::unexpected($object, Diary::class);
        }

        $diary = new DiaryModel();

        $client = null;
        $money = null;
        $order = null;

        if ($object->getClient()) {
            $client = $this->modelTransformer->transform($object->getClient());
        }

        if ($object->getMoney()) {
            $money = $this->modelTransformer->transform($object->getMoney());
        }

        if ($object->getOrder()) {
            $order = $this->modelTransformer->transform($object->getOrder());
        }

        Reflection::setPropertiesValue($diary, [
            'id' => $object->getId(),
            'order' => $order,
            'creator' => $this->modelTransformer->transform($object->getCreator()),
            'client' => $client,
            'factories' => $this->modelTransformer->transform($object->getFactories()),
            'createdAt' => $object->getCreatedAt(),
            'updatedAt' => $object->getUpdatedAt(),
            'removedAt' => $object->getRemovedAt(),
            'money' => $money,
            'documentNumber' => $object->getDocumentNumber(),
            'comment' => $object->getComment(),
            'attachments' => $this->modelTransformer->transform($object->getAttachments())
        ]);

        return $diary;
    }

    /**
     * {@inheritDoc}
     */
    public function supportsClass($class)
    {
        return is_a($class, Diary::class, true);
    }
}
