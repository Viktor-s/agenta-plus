<?php

namespace AgentPlus\Api\Internal\Diary\Request;

use FiveLab\Component\Api\Request\RequestInterface;
use FiveLab\Component\ObjectMapper\Annotation as DataMapping;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @DataMapping\Object(allProperties=true)
 */
class DiarySearchRequest implements RequestInterface
{
    /**
     * @var array
     */
    private $factories = [];

    /**
     * @var array
     */
    private $creators = [];

    /**
     * @var array
     */
    private $clients = [];

    /**
     * @var array
     */
    private $stages = [];

    /**
     * @var int
     *
     * @Assert\NotBlank
     * @Assert\Range(min = 1)
     */
    private $page = 1;

    /**
     * @var int
     *
     * @Assert\NotBlank
     * @Assert\Range(min = 10, max = 100)
     */
    private $limit = 50;

    /**
     * Get factory ids
     *
     * @return array
     */
    public function getFactoryIds()
    {
        return $this->factories ?: [];
    }

    /**
     * Get creator ids
     *
     * @return array
     */
    public function getCreatorIds()
    {
        return $this->creators ?: [];
    }

    /**
     * Get client ids
     *
     * @return array
     */
    public function getClientIds()
    {
        return $this->clients ?: [];
    }

    /**
     * Get stage ids
     *
     * @return array
     */
    public function getStageIds()
    {
        return $this->stages ?: [];
    }

    /**
     * Get page
     *
     * @return int
     */
    public function getPage()
    {
        return $this->page;
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
