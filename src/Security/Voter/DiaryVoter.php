<?php

namespace AgentPlus\Security\Voter;

use AgentPlus\Entity\Diary\Diary;
use AgentPlus\Entity\User\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class DiaryVoter implements VoterInterface
{
    /**
     * {@inheritDoc}
     */
    public function supportsAttribute($attribute)
    {
        return in_array($attribute, ['DIARY_CREATE', 'DIARY_EDIT', 'DIARY_REMOVE', 'DIARY_RESTORE']);
    }

    /**
     * {@inheritDoc}
     */
    public function supportsClass($class)
    {
    }

    public function vote(TokenInterface $token, $object, array $attributes)
    {
        $user = $token->getUser();

        if (!$user || !$user instanceof User) {
            return self::ACCESS_ABSTAIN;
        }

        if ($object && !$object instanceof Diary) {
            return self::ACCESS_ABSTAIN;
        }

        if (in_array('DIARY_CREATE', $attributes)) {
            return $user->isAgent() || $user->isEmployee() ? self::ACCESS_GRANTED : self::ACCESS_DENIED;
        }

        if (in_array('DIARY_EDIT', $attributes) || in_array('DIARY_REMOVE', $attributes) || in_array('DIARY_RESTORE', $attributes)) {
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
