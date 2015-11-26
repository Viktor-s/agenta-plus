<?php

namespace AgentPlus\Api\Internal\Diary\Request;

use AgentPlus\Api\Request\DateTimeInterval;
use FiveLab\Component\Api\Request\RequestInterface;
use FiveLab\Component\ObjectMapper\Annotation as DataMapping;
use Symfony\Component\Validator\Constraints as Assert;

class DiarySearchRequest implements RequestInterface
{
    /**
     * @var array
     *
     * @DataMapping\Property()
     */
    private $types = [];

    /**
     * @var array
     *
     * @DataMapping\Property()
     */
    private $factories = [];

    /**
     * @var array
     *
     * @DataMapping\Property()
     */
    private $creators = [];

    /**
     * @var array
     *
     * @DataMapping\Property()
     */
    private $clients = [];

    /**
     * @var array
     *
     * @DataMapping\Property()
     */
    private $stages = [];

    /**
     * @var array
     *
     * @DataMapping\Property()
     */
    private $countries = [];

    /**
     * @var array
     *
     * @DataMapping\Property()
     */
    private $cities = [];

    /**
     * @var DateTimeInterval
     *
     * @DataMapping\Property(class="AgentPlus\Api\Request\DateTimeInterval")
     */
    private $created;

    /**
     * @var int
     *
     * @DataMapping\Property()
     *
     * @Assert\NotBlank
     * @Assert\Range(min = 1)
     */
    private $page = 1;

    /**
     * @var int
     *
     * @DataMapping\Property()
     *
     * @Assert\NotBlank
     * @Assert\Range(min = 10, max = 100)
     */
    private $limit = 50;

    /**
     * Construct
     */
    public function __construct()
    {
        $this->created = new DateTimeInterval();
    }

    /**
     * Get type ids
     *
     * @return array
     */
    public function getTypeIds()
    {
        return $this->types ?: [];
    }

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
     * Get countries
     *
     * @return array
     */
    public function getCountryCodes()
    {
        return $this->countries;
    }

    /**
     * Get cities
     *
     * @return array
     */
    public function getCities()
    {
        return $this->cities;
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

    /**
     * Get created
     *
     * @return DateTimeInterval
     */
    public function getCreated()
    {
        return $this->created;
    }
}
