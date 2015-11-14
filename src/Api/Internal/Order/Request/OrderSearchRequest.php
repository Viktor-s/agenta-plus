<?php

namespace AgentPlus\Api\Internal\Order\Request;

use FiveLab\Component\Api\Request\RequestInterface;
use FiveLab\Component\ObjectMapper\Annotation as DataMapping;
use Symfony\Component\Validator\Constraints as Assert;

class OrderSearchRequest implements RequestInterface
{
    /**
     * @var int
     *
     * @DataMapping\Property()
     *
     * @Assert\NotBlank
     * @Assert\Range(min = 1)
     */
    private $page = 1;

    /**
     * @var int
     *
     * @DataMapping\Property()
     *
     * @Assert\NotBlank()
     * @Assert\Range(min = 10, max = 100)
     */
    private $limit = 50;

    /**
     * Get page
     *
     * @return int
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * Get limit
     *
     * @return int
     */
    public function getLimit()
    {
        return $this->limit;
    }
}
