<?php

namespace AgentPlus\Model\Normalizer;

use AgentPlus\Model\Diary\Diary;
use FiveLab\Component\ModelNormalizer\ContextInterface;
use FiveLab\Component\ModelNormalizer\Exception\DenormalizationFailedException;
use FiveLab\Component\ModelNormalizer\Exception\NormalizationFailedException;
use FiveLab\Component\ModelNormalizer\ModelNormalizerInterface;
use FiveLab\Component\ModelNormalizer\ModelNormalizerManagerAwareInterface;
use FiveLab\Component\ModelNormalizer\ModelNormalizerManagerInterface;

class DiaryModelNormalizer implements ModelNormalizerInterface, ModelNormalizerManagerAwareInterface
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
        if (!$object instanceof Diary) {
            throw NormalizationFailedException::unexpected($object, Diary::class);
        }

        $client = null;
        $money = null;
        $order = null;
        $removedAt = null;
        $stage = null;
        $type = null;

        if ($object->getType()) {
            $type = $this->modelNormalizer->normalize($object->getType());
        }

        if ($object->getStage()) {
            $stage = $this->modelNormalizer->normalize($object->getStage());
        }

        if ($object->getClient()) {
            $client = $this->modelNormalizer->normalize($object->getClient());
        }

        if ($object->getMoney()) {
            $money = $this->modelNormalizer->normalize($object->getMoney());
        }

        if ($object->getOrder()) {
            $order = $this->modelNormalizer->normalize($object->getOrder());
        }

        if ($object->getRemovedAt()) {
            $removedAt = $this->modelNormalizer->normalize($object->getRemovedAt());
        }

        $data = [
            'id' => $object->getId(),
            'type' => $type,
            'order' => $order,
            'client' => $client,
            'stage' => $stage,
            'creator' => $this->modelNormalizer->normalize($object->getCreator()),
            'factories' => $this->modelNormalizer->normalize($object->getFactories()),
            'createdAt' => $this->modelNormalizer->normalize($object->getCreatedAt()),
            'updatedAt' => $this->modelNormalizer->normalize($object->getUpdatedAt()),
            'removedAt' => $removedAt,
            'money' => $money,
            'documentNumber' => $object->getDocumentNumber(),
            'comment' => $object->getComment(),
            'attachments' => $this->modelNormalizer->normalize($object->getAttachments())
        ];

        return $data;
    }

    /**
     * {@inheritDoc}
     */
    public function denormalize($data, ContextInterface $context)
    {
        throw new DenormalizationFailedException('Not implement.');
    }

    /**
     * {@inheritDoc}
     */
    public function supportsClass($class)
    {
        return is_a($class, Diary::class, true);
    }
}
