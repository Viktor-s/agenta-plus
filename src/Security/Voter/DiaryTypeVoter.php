<?php

namespace AgentPlus\Security\Voter;

use AgentPlus\Entity\Diary\Type;
use AgentPlus\Entity\User\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class DiaryTypeVoter implements VoterInterface
{
    /**
     * {@inheritDoc}
     */
    public function supportsAttribute($attribute)
    {
        return in_array($attribute, [
            'DIARY_TYPE_CREATE',
            'DIARY_TYPE_EDIT',
            'DIARY_TYPE_REMOVE'
        ]);
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

        if ($object && !$object instanceof Type) {
            return self::ACCESS_ABSTAIN;
        }

        if (in_array('DIARY_TYPE_CREATE', $attributes)) {
            return $user->isAgent() ? self::ACCESS_GRANTED : self::ACCESS_DENIED;
        }

        if (in_array('DIARY_TYPE_EDIT', $attributes) || in_array('DIARY_TYPE_REMOVE', $attributes)) {
            if (!$object) {
                return self::ACCESS_ABSTAIN;
            }

            if ($user->isAgent()) {
                return self::ACCESS_GRANTED;
            }

            return self::ACCESS_DENIED;
        }

        return self::ACCESS_ABSTAIN;
    }
}
