<?php

namespace AgentPlus\Api\Internal\Catalog\Request;

use FiveLab\Component\Api\Request\RequestInterface;
use FiveLab\Component\ObjectMapper\Annotation as DataMapping;
use Symfony\Component\Validator\Constraints as Assert;

class CatalogActionRequest implements RequestInterface
{
    /**
     * @var string
     *
     * @DataMapping\Property()
     *
     * @Assert\NotBlank
     */
    protected $id;

    /**
     * Get catalog id
     *
     * @return string
     */
    public function getCatalogId()
    {
        return $this->id;
    }
}
