<?php

namespace AgentPlus\Api\Internal\Diary\Request;

use FiveLab\Component\ObjectMapper\Annotation as DataMapping;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @DataMapping\Object(allProperties=true)
 */
class TypeUpdateRequest extends TypeActionRequest
{
    /**
     * @var string
     */
    private $parent;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @Assert\Length(min = 2, max = 255)
     */
    private $name;

    /**
     * @var int
     *
     * @Assert\NotNull()
     */
    private $position = 0;

    /**
     * Get parent id
     *
     * @return string
     */
    public function getParentId()
    {
        return $this->parent;
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
     * Get position
     *
     * @return int
     */
    public function getPosition()
    {
        return $this->position;
    }
}
