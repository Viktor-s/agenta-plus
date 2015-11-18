<?php

namespace AgentPlus\Api\Internal\Catalog\Request;

use FiveLab\Component\Api\Request\RequestInterface;
use FiveLab\Component\ObjectMapper\Annotation as DataMapping;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Request for create catalog
 */
class CatalogCreateRequest implements RequestInterface
{
    /**
     * @var string
     *
     * @DataMapping\Property()
     *
     * @Assert\NotBlank()
     * @Assert\Length(min = 1, max = 255)
     */
    private $name;

    /**
     * @var array
     *
     * @DataMapping\Property()
     */
    private $factories;

    /**
     * @var \AgentPlus\Api\Request\Attachment[]
     *
     * @DataMapping\Property(class="AgentPlus\Api\Request\Attachment", collection=true)
     *
     * @Assert\Valid()
     */
    private $images;

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
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
     * Get images
     *
     * @return array|\AgentPlus\Api\Request\Attachment[]
     */
    public function getImages()
    {
        return $this->images ?: [];
    }
}
