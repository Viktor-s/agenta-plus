<?php

namespace AgentPlus\Model\Order;

use FiveLab\Component\ModelNormalizer\Annotation as ModelNormalize;

/**
 * @ModelNormalize\Object(allProperties=true)
 */
class Money
{
    /**
     * @var string
     */
    private $currency;

    /**
     * @var float
     */
    private $amount;
}
