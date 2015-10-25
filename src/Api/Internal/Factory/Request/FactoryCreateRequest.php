<?php

namespace AgentPlus\Api\Internal\Factory\Request;

use FiveLab\Component\Api\Request\RequestInterface;
use FiveLab\Component\ObjectMapper\Annotation as DataMapping;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @DataMapping\Object(allProperties=true)
 */
class FactoryCreateRequest implements RequestInterface
{
    /**
     * Name of factory
     *
     * @var string
     *
     * @Assert\NotBlank
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
}
