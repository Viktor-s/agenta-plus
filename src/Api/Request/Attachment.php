<?php

namespace AgentPlus\Api\Request;

use FiveLab\Component\ObjectMapper\Annotation as DataMapper;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Attachment request
 *
 * @DataMapper\Object(allProperties=true)
 */
class Attachment
{
    /**
     * @var string
     *
     * @Assert\NotBlank()
     */
    private $name;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     */
    private $path;

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
     * Get path
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }
}
