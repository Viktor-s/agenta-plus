<?php

namespace AgentPlus\Model\Diary;

class Diary
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var Type
     */
    private $type;

    /**
     * @var \AgentPlus\Model\User\User
     */
    private $creator;

    /**
     * @var \AgentPlus\Model\Client\Client
     */
    private $client;

    /**
     * @var \AgentPlus\Model\Order\Order
     */
    private $order;

    /**
     * @var \AgentPlus\Model\Order\Stage
     */
    private $stage;

    /**
     * @var \AgentPlus\Model\Collection|\AgentPlus\Model\Factory\Factory[]
     */
    private $factories;

    /**
     * @var \DateTime
     */
    private $createdAt;

    /**
     * @var \DateTime
     */
    private $updatedAt;

    /**
     * @var \DateTime
     */
    private $removedAt;

    /**
     * @var Money
     */
    private $money;

    /**
     * @var string
     */
    private $documentNumber;

    /**
     * @var string
     */
    private $comment;

    /**
     * @var \AgentPlus\Model\Collection|\AgentPlus\Model\Attachment[]
     */
    private $attachments;

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
     * Get type
     *
     * @return Type
     */
    public function getType()
    {
        return $this->type;
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
     * Get order
     *
     * @return \AgentPlus\Model\Order\Order
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * Get stage
     *
     * @return \AgentPlus\Model\Order\Stage
     */
    public function getStage()
    {
        return $this->stage;
    }

    /**
     * Get factories
     *
     * @return \AgentPlus\Model\Collection|\AgentPlus\Model\Factory\Factory[]
     */
    public function getFactories()
    {
        return $this->factories;
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
     * Get removed at
     *
     * @return \DateTime
     */
    public function getRemovedAt()
    {
        return $this->removedAt;
    }

    /**
     * Get money
     *
     * @return Money|null
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
     * Get document number
     *
     * @return string
     */
    public function getDocumentNumber()
    {
        return $this->documentNumber;
    }

    /**
     * Get attachments
     *
     * @return \AgentPlus\Model\Collection|\AgentPlus\Model\Attachment[]
     */
    public function getAttachments()
    {
        return $this->attachments;
    }
}
