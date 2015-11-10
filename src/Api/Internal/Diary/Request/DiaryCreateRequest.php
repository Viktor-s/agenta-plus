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
    private $client;

    /**
     * @var array
     *
     * @DataMapping\Property()
     */
    private $factories = [];

    /**
     * @var Money
     *
     * @DataMapping\Property(class="AgentPlus\Api\Internal\Diary\Request\Money")
     *
     * @Assert\Valid()
     */
    private $money;

    /**
     * @var string
     *
     * @DataMapping\Property()
     */
    private $documentNumber;

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
     * Get document number
     *
     * @return string
     */
    public function getDocumentNumber()
    {
        return $this->documentNumber;
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
     * Has attachments?
     *
     * @return bool
     */
    public function hasAttachments()
    {
        return $this->attachments && count($this->attachments) > 0;
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
