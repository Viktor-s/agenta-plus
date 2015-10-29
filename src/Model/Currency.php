<?php

namespace AgentPlus\Model;

use FiveLab\Component\ModelNormalizer\Annotation as ModelNormalize;

/**
 * @ModelNormalize\Object(allProperties=true)
 */
class Currency
{
    /**
     * @var string
     */
    private $code;

    /**
     * @var int
     */
    private $position;
}
