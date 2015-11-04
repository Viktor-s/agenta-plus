<?php

namespace AgentPlus\Model\Diary;

class Diary
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var \AgentPlus\Model\User\User
     */
    private $creator;

    /**
     * @var \AgentPlus\Model\Client\Client
     */
    private $client;

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
    private $comment;

    /**
     * @var \AgentPlus\Model\Collection|Attachment[]
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
     * Get attachments
     *
     * @return \AgentPlus\Model\Collection|Attachment[]
     */
    public function getAttachments()
    {
        return $this->attachments;
    }
}
