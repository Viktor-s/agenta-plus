<?php

namespace AgentPlus\Api\Internal\Factory\Request;

use FiveLab\Component\ObjectMapper\Annotation as DataMapping;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @DataMapping\Object(allProperties=true)
 */
class FactoryUpdateRequest extends FactoryActionRequest
{
    /**
     * @var string
     *
     * @Assert\Length(max = 255)
     */
    private $name;

    /**
     * Has name?
     *
     * @return bool
     */
    public function hasName()
    {
        return (bool) $this->name;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}
