<?php

namespace AgentPlus\Api\Internal\Team\Request;

use FiveLab\Component\Api\Request\RequestInterface;
use FiveLab\Component\ObjectMapper\Annotation as DataMapping;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @DataMapping\Object(allProperties=true)
 */
class TeamCreateRequest implements RequestInterface
{
    /**
     * The team name
     *
     * @var string
     *
     * @Assert\NotBlank
     * @Assert\Length(max = 255)
     */
    private $name;

    /**
     * Get team name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}
