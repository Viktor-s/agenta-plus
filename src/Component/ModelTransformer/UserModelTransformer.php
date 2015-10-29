<?php

namespace AgentPlus\Component\ModelTransformer;

use AgentPlus\Entity\User\User;
use AgentPlus\Model\Collection;
use AgentPlus\Model\User\User as UserModel;
use FiveLab\Component\ModelTransformer\ContextInterface;
use FiveLab\Component\ModelTransformer\Exception\TransformationFailedException;
use FiveLab\Component\ModelTransformer\ModelTransformerInterface;
use FiveLab\Component\ModelTransformer\ModelTransformerManagerAwareInterface;
use FiveLab\Component\ModelTransformer\ModelTransformerManagerInterface;
use FiveLab\Component\Reflection\Reflection;

class UserModelTransformer implements ModelTransformerInterface, ModelTransformerManagerAwareInterface
{
    /**
     * @var ModelTransformerManagerInterface
     */
    private $modelTransformer;

    /**
     * Set model transformer
     *
     * @param ModelTransformerManagerInterface $manager
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
        if (!$object instanceof User) {
            throw TransformationFailedException::unexpected($object, User::class);
        }

        $userModel = new UserModel();

        Reflection::setPropertiesValue($userModel, [
            'id' => $object->getId(),
            'username' => $object->getUsername(),
            'email' => $object->getEmail(),
            'createdAt' => $object->getCreatedAt(),
            'type' => Reflection::getPropertyValue($object, 'type')
        ]);

        if ($object->isFactory() || $object->isEmployee()) {
            $teams = [];

            foreach ($object->getTeams() as $team) {
                $teams[] = $this->modelTransformer->transform($team);
            }

            $teams = new Collection($teams);
            Reflection::setPropertyValue($userModel, 'teams', $teams);
        }

        return $userModel;
    }

    /**
     * {@inheritDoc}
     */
    public function supportsClass($class)
    {
        return is_a($class, User::class, true);
    }
}
