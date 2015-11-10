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
     * @var \AgentPlus\Model\Client\Client
     */
    private $client;

    /**
     * @var \AgentPlus\Model\User\User
     */
    private $creator;

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
}
