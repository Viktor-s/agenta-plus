<?php

namespace AgentPlus\Api\Internal\Team\Request;

use FiveLab\Component\Api\Request\RequestInterface;
use FiveLab\Component\ObjectMapper\Annotation as DataMapping;
use Symfony\Component\Validator\Constraints as Assert;

class TeamActionRequest implements RequestInterface
{
    /**
     * @var string
     *
     * @DataMapping\Property()
     *
     * @Assert\NotBlank()
     */
    private $id;

    /**
     * Get team key
     *
     * @return string
     */
    public function getTeamId()
    {
        return $this->id;
    }
}
