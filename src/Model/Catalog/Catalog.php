<?php

namespace AgentPlus\Model\Catalog;

use FiveLab\Component\ModelNormalizer\Annotation as ModelNormalize;

class Catalog
{
    /**
     * @var string
     *
     * @ModelNormalize\Property()
     */
    private $id;

    /**
     * @var \AgentPlus\Model\User\User
     *
     * @ModelNormalize\Property(shouldNormalize=true)
     */
    private $creator;

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

    /**
     * @var \AgentPlus\Model\Collection|\AgentPlus\Model\Factory\Factory[]
     *
     * @ModelNormalize\Property(shouldNormalize=true)
     */
    private $factories;

    /**
     * @var \AgentPlus\Model\Collection|\AgentPlus\Model\Attachment[]
     *
     * @ModelNormalize\Property(shouldNormalize=true)
     */
    private $images;
}
