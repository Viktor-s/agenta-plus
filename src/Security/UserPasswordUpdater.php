<?php

namespace AgentPlus\Security;

use AgentPlus\Entity\User\User;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;

/**
 * User password updater
 */
class UserPasswordUpdater
{
    /**
     * @var EncoderFactoryInterface
     */
    private $encoderFactory;

    /**
     * Construct
     *
     * @param EncoderFactoryInterface $encoder
     */
    public function __construct(EncoderFactoryInterface $encoder)
    {
        $this->encoderFactory = $encoder;
    }

    /**
     * Update password for user
     *
     * @param User $user
     */
    public function update(User $user)
    {
        if (!$user->getPlainPassword()) {
            return;
        }

        static $ref;

        if (!$ref) {
            $ref = new \ReflectionProperty(User::class, 'password');
            $ref->setAccessible(true);
        }

        $password = $this->encoderFactory->getEncoder($user)
            ->encodePassword($user->getPlainPassword(), $user->getSalt());

        $ref->setValue($user, $password);
    }
}
