<?php

namespace AgentPlus\Api\Internal\Factory\Request;

use FiveLab\Component\Api\Request\RequestInterface;
use FiveLab\Component\ObjectMapper\Annotation as DataMapping;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @DataMapping\Object(allProperties=true)
 */
class FactoryActionRequest implements RequestInterface
{
    /**
     * @var string
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