<?php

namespace AgentPlus\Api\Internal\Stage\Request;

use FiveLab\Component\Api\Request\RequestInterface;
use FiveLab\Component\ObjectMapper\Annotation as DataMapping;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @DataMapping\Object(allProperties=true)
 */
class StageCreateRequest implements RequestInterface
{
    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @Assert\Length(max=255)
     */
    private $label;

    /**
     * @var int
     */
    private $position = 0;

    /**
     * Get name
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
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
