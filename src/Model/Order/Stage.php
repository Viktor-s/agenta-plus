<?php

namespace AgentPlus\Model\Order;

use FiveLab\Component\ModelNormalizer\Annotation as ModelNormalize;

/**
 * @ModelNormalize\Object(allProperties=true)
 */
class Stage
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $label;

    /**
     * @var int
     */
    private $position;
}
