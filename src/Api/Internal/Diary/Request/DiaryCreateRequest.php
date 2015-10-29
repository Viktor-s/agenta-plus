<?php

namespace AgentPlus\Api\Internal\Diary\Request;

use FiveLab\Component\Api\Request\RequestInterface;
use FiveLab\Component\ObjectMapper\Annotation as DataMapping;
use Symfony\Component\Validator\Constraints as Assert;

class DiaryCreateRequest implements RequestInterface
{
    /**
     * @var string
     *
     * @DataMapping\Property()
     */
    protected $client;

    /**
     * @var array
     *
     * @DataMapping\Property()
     */
    protected $factories = [];

    /**
     * @var Money
     *
     * @DataMapping\Property(class="AgentPlus\Api\Internal\Diary\Request\Money")
     *
     * @Assert\Valid()
     */
    protected $money;

    /**
     * @var string
     *
     * @DataMapping\Property()
     */
    protected $comment;

    /**
     * Has client?
     *
     * @return bool
     */
    public function hasClient()
    {
        return (bool) $this->client;
    }

    /**
     * Get client id
     *
     * @return string
     */
    public function getClientId()
    {
        return $this->client;
    }

    /**
     * Has factories?
     *
     * @return bool
     */
    public function hasFactories()
    {
        return $this->factories && count($this->factories) > 0;
    }

    /**
     * Get factories
     *
     * @return array
     */
    public function getFactoriesIds()
    {
        return $this->factories;
    }

    /**
     * Has money?
     *
     * @return bool
     */
    public function hasMoney()
    {
        return $this->money && $this->money->getAmount() > 0;
    }

    /**
     * Get money
     *
     * @return Money
     */
    public function getMoney()
    {
        return $this->money;
    }

    /**
     * Get comment
     *
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }
}
