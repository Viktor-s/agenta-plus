<?php

namespace AgentPlus\Model;

use FiveLab\Component\ModelNormalizer\Annotation as ModelNormalize;

/**
 * @ModelNormalize\Object(allProperties=true)
 */
class Country
{
    /**
     * @var string
     */
    private $code;

    /**
     * @var string
     */
    private $name;

    /**
     * Construct
     *
     * @param string $code
     * @param string $name
     */
    public function __construct($code, $name)
    {
        $this->code = $code;
        $this->name = $name;
    }
}
