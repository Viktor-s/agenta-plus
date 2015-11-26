<?php

namespace AgentPlus\Api\Internal\Diary\Request;

use FiveLab\Component\Api\Request\RequestInterface;
use FiveLab\Component\ObjectMapper\Annotation as DataMapping;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @DataMapping\Object(allProperties=true)
 */
class TypeCreateRequest implements RequestInterface
{
    /**
     * @var string
     */
    private $parent;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @Assert\Length(min = 2, max= 255)
     */
    private $name;

    /**
     * Get parent id
     *
     * @return string
     */
    public function getParent()
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
}
