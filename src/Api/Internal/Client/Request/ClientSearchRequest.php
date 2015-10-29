<?php

namespace AgentPlus\Api\Internal\Client\Request;

use FiveLab\Component\Api\Request\RequestInterface;
use FiveLab\Component\ObjectMapper\Annotation as DataMapping;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @DataMapping\Object(allProperties=true)
 */
class ClientSearchRequest implements RequestInterface
{
    /**
     * @var int
     */
    private $page;

    /**
     * @var int
     *
     * @Assert\NotBlank()
     * @Assert\Range(min = 10, max = 100)
     */
    private $limit = 50;

    /**
     * Get page
     *
     * @return int
     */
    public function getPage()
    {
        return $this->page > 0 ? $this->page : null;
    }

    /**
     * Get limit
     *
     * @return int
     */
    public function getLimit()
    {
        return $this->limit;
    }
}