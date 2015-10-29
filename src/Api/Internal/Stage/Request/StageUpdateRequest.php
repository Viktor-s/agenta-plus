<?php

namespace AgentPlus\Api\Internal\Stage\Request;

use FiveLab\Component\ObjectMapper\Annotation as DataMapping;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @DataMapping\Object(allProperties=true)
 */
class StageUpdateRequest extends StageActionRequest
{
    /**
     * @var string
     *
     * @Assert\Length(max = 255)
     */
    private $name;

    /**
     * @var int
     */
    private $position;

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

    /**
     * Has position
     *
     * @return bool
     */
    public function hasPosition()
    {
        return null !== $this->position;
    }

    /**
     * Get position
     *
     * @return int
     */
    public function getPosition()
    {
        return $this->position;
    }
}
