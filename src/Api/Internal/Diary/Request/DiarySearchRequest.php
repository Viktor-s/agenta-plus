<?php

namespace AgentPlus\Api\Internal\Diary\Request;

use FiveLab\Component\Api\Request\RequestInterface;
use FiveLab\Component\ObjectMapper\Annotation as DataMapping;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @DataMapping\Object(allProperties=true)
 */
class DiarySearchRequest implements RequestInterface
{
    /**
     * @var int
     *
     * @Assert\NotBlank
     * @Assert\Range(min = 1)
     */
    private $page = 1;

    /**
     * @var int
     *
     * @Assert\NotBlank
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
