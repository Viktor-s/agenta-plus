<?php

namespace AgentPlus\Security\Voter;

use AgentPlus\Entity\Diary\Diary;
use AgentPlus\Entity\User\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class OrderVoter implements VoterInterface
{
    /**
     * {@inheritDoc}
     */
    public function supportsAttribute($attribute)
    {
        return in_array($attribute, ['ORDER_CREATE', 'ORDER_EDIT']);
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

        if ($object && !$object instanceof Diary) {
            return self::ACCESS_ABSTAIN;
        }

        if (in_array('ORDER_CREATE', $attributes)) {
            return $user->isAgent() || $user->isEmployee() ? self::ACCESS_GRANTED : self::ACCESS_DENIED;
        }

        if (in_array('ORDER_EDIT', $attributes)) {
            if (!$object) {
                return self::ACCESS_ABSTAIN;
            }

            if ($user->isAgent()) {
                return self::ACCESS_GRANTED;
            }

            if ($user->isEmployee()) {
                $creator = $object->getCreator();

                return $creator->getId() == $user->getId() ? self::ACCESS_GRANTED : self::ACCESS_DENIED;
            }

            return self::ACCESS_DENIED;
        }

        return self::ACCESS_ABSTAIN;
    }
}
