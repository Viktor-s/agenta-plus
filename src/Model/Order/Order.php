<?php

namespace AgentPlus\Model\Order;

class Order
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var \DateTime
     */
    private $createdAt;

    /**
     * @var \DateTime
     */
    private $updatedAt;

    /**
     * @var \AgentPlus\Model\Client\Client
     */
    private $client;

    /**
     * @var \AgentPlus\Model\User\User
     */
    private $creator;

    /**
     * @var \AgentPlus\Model\Factory\Factory
     */
    private $factory;

    /**
     * @var Stage
     */
    private $stage;

    /**
     * @var Money
     */
    private $money;

    /**
     * Get id
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get created at
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Get updated at
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Get creator
     *
     * @return \AgentPlus\Model\User\User
     */
    public function getCreator()
    {
        return $this->creator;
    }

    /**
     * Get client
     *
     * @return \AgentPlus\Model\Client\Client
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * Get factories
     *
     * @return \AgentPlus\Model\Collection|\AgentPlus\Model\Factory\Factory[]
     */
    public function getFactory()
    {
        return $this->factory;
    }

    /**
     * Get stage
     *
     * @return Stage
     */
    public function getStage()
    {
        return $this->stage;
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
}
