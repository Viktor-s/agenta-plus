<?php

namespace AgentPlus\Model\User;

use FiveLab\Component\ModelNormalizer\Annotation as ModelNormalize;

class User
{
    /**
     * @var string
     *
     * @ModelNormalize\Property()
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ModelNormalize\Property(shouldNormalize=true)
     */
    private $createdAt;

    /**
     * @var string
     *
     * @ModelNormalize\Property()
     */
    private $username;

    /**
     * @var string
     *
     * @ModelNormalize\Property()
     */
    private $email;

    /**
     * @var int
     *
     * @ModelNormalize\Property()
     */
    private $type;

    /**
     * @var \AgentPlus\Model\Collection
     *
     * @ModelNormalize\Property(shouldNormalize=true)
     */
    private $teams;
}
