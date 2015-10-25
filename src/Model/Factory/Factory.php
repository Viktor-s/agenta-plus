<?php

namespace AgentPlus\Model\Factory;

use FiveLab\Component\ModelNormalizer\Annotation as ModelNormalize;

/**
 * @ModelNormalize\Object(allProperties=true)
 */
class Factory
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $name;
}
