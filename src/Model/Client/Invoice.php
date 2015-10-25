<?php

namespace AgentPlus\Model\Client;

use FiveLab\Component\ModelNormalizer\Annotation as ModelNormalize;

/**
 * @ModelNormalize\Object(allProperties=true)
 */
class Invoice
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
