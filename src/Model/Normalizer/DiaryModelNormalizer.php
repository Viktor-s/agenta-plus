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

        if ($object->getClient()) {
            $client = $this->modelNormalizer->normalize($object->getClient());
        }

        if ($object->getMoney()) {
            $money = $this->modelNormalizer->normalize($object->getMoney());
        }

        $data = [
            'id' => $object->getId(),
            'client' => $client,
            'creator' => $this->modelNormalizer->normalize($object->getCreator()),
            'factories' => $this->modelNormalizer->normalize($object->getFactories()),
            'createdAt' => $this->modelNormalizer->normalize($object->getCreatedAt()),
            'updatedAt' => $this->modelNormalizer->normalize($object->getUpdatedAt()),
            'money' => $money,
            'comment' => $object->getComment()
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
