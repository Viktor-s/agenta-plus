<?php

namespace AgentPlus\Security\Voter;

use AgentPlus\Entity\Team;
use AgentPlus\Entity\User;
use Symfony\Component\Security\Core\Authorization\Voter\AbstractVoter;

/**
 * Team voter.
 * Only team owner have a access to edit and remove team.
 */
class TeamVoter extends AbstractVoter
{
    /**
     * {@inheritDoc}
     */
    protected function getSupportedClasses()
    {
        return [Team::class];
    }

    /**
     * {@inheritDoc}
     */
    protected function getSupportedAttributes()
    {
        return ['EDIT', 'REMOVE'];
    }

    /**
     * {@inheritDoc}
     */
    protected function isGranted($attribute, $object, $user = null)
    {
        /** @var Team $object */
        if (!$user || !$user instanceof User) {
            return false;
        }

        $owner = $object->getOwner();

        return $owner->getId() == $user->getId();
    }
}
