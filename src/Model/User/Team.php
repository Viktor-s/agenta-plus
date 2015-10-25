<?php

namespace AgentPlus\Model\User;

use FiveLab\Component\ModelNormalizer\Annotation as ModelNormalize;

/**
 * Team model
 *
 * @ModelNormalize\Object()
 */
class Team
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
    private $name;
}
