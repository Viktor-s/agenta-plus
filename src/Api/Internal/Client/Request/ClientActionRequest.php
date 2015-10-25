<?php

namespace AgentPlus\Api\Internal\Client\Request;

use FiveLab\Component\Api\Request\RequestInterface;
use FiveLab\Component\ObjectMapper\Annotation as DataMapping;
use Symfony\Component\Validator\Constraints as Assert;

class ClientActionRequest implements RequestInterface
{
    /**
     * @var string
     *
     * @DataMapping\Property()
     *
     * @Assert\NotBlank
     */
    protected $id;

    /**
     * Get id
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }
}
