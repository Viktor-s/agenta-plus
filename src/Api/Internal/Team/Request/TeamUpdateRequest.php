<?php

namespace AgentPlus\Api\Internal\Team\Request;

use FiveLab\Component\ObjectMapper\Annotation as DataMapping;
use Symfony\Component\Validator\Constraints as Assert;

class TeamUpdateRequest extends TeamActionRequest
{
    /**
     * @var string
     *
     * @DataMapping\Property()
     *
     * @Assert\Length(max = 255)
     */
    private $name;

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Has name?
     *
     * @return bool
     */
    public function hasName()
    {
        return (bool) $this->name;
    }
}
