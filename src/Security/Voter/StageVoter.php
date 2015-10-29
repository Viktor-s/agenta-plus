<?php

namespace AgentPlus\Security\Voter;

use AgentPlus\Entity\User\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class StageVoter implements VoterInterface
{
    /**
     * {@inheritDoc}
     */
    public function supportsAttribute($attribute)
    {
        return in_array($attribute, ['STAGE_CREATE', 'STAGE_EDIT', 'STAGE_REMOVE']);
    }

    /**
     * {@inheritDoc}
     */
    public function supportsClass($class)
    {
    }

    /**
     * {@inheritDoc}
     */
    public function vote(TokenInterface $token, $object, array $attributes)
    {
        $user = $token->getUser();

        if (!$user || !$user instanceof User) {
            return self::ACCESS_ABSTAIN;
        }

        if (in_array('STAGE_CREATE', $attributes)) {
            return $user->isAgent() ? self::ACCESS_GRANTED : self::ACCESS_DENIED;
        }

        if (in_array('STAGE_EDIT', $attributes)) {
            return $user->isAgent() ? self::ACCESS_GRANTED : self::ACCESS_DENIED;
        }

        if (in_array('STAGE_REMOVE', $attributes)) {
            return $user->isAgent() ? self::ACCESS_GRANTED : self::ACCESS_DENIED;
        }

        return self::ACCESS_ABSTAIN;
    }
}
