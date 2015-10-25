<?php

namespace AgentPlus\Api\Internal\Profile;

use FiveLab\Component\Api\Annotation\Action;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class ProfileApi
{
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * Construct
     *
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * View profile
     *
     * @Action("profile")
     */
    public function profile()
    {
        $user = $this->tokenStorage->getToken()->getUser();

        return $user;
    }
}
