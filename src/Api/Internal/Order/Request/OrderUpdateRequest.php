<?php

namespace AgentPlus\Api\Internal\Order\Request;

use FiveLab\Component\Api\Request\RequestInterface;
use FiveLab\Component\ObjectMapper\Annotation as DataMapping;
use Symfony\Component\Validator\Constraints as Assert;

class OrderUpdateRequest extends OrderActionRequest
{
    /**
     * @var string
     *
     * @DataMapping\Property()
     *
     * @Assert\NotBlank()
     */
    private $stage;

    /**
     * @var array
     *
     * @DataMapping\Property()
     */
    private $factories = [];

    /**
     * @var Money
     *
     * @DataMapping\Property(class="AgentPlus\Api\Internal\Order\Request\Money")
     *
     * @Assert\NotBlank()
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
     * @var string
     *
     * @DataMapping\Property()
     */
    private $documentNumber;

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
     * Get factory ids
     *
     * @return array
     */
    public function getFactoryIds()
    {
        return $this->factories ?: [];
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
     * @return array|\AgentPlus\Api\Request\Attachment[]
     */
    public function getAttachments()
    {
        return $this->attachments;
    }
}
