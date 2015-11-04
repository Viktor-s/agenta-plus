<?php

namespace AgentPlus\Api\Internal\Order\Request;

use FiveLab\Component\Api\Request\RequestInterface;
use FiveLab\Component\ObjectMapper\Annotation as DataMapping;

class OrderCreateRequest implements RequestInterface
{
    /**
     * @var string
     *
     * @DataMapping\Property()
     */
    private $stage;

    /**
     * @var string
     *
     * @DataMapping\Property()
     */
    private $client;

    /**
     * @var array
     *
     * @DataMapping\Property()
     */
    private $factories;

    /**
     * @var Money
     *
     * @DataMapping\Property(class="AgentPlus\Api\Internal\Order\Money")
     *
     * @Assert\Valid
     */
    private $money;

    /**
     * @var string
     *
     * @DataMapping\Property()
     */
    private $comment;

    /**
     * @DataMapping\Property(class="AgentPlus\Api\Request\Attachment", collection=true)
     */
    private $attachments = [];

    /**
     * Construct
     */
    public function __construct()
    {
        $this->money = new Money();
    }


    /**
     * Get stage id
     *
     * @return string
     */
    public function getStageId()
    {
        return $this->stage;
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
     * Get factory ids
     *
     * @return array
     */
    public function getFactoryIds()
    {
        return $this->factories;
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

    /**
     * Get attachments
     *
     * @return array|\AgentPlus\Api\Request\Attachment[]
     */
    public function getAttachments()
    {
        return $this->attachments;
    }
}
