<?php

namespace AgentPlus\Api\Internal\Diary\Request;

use FiveLab\Component\Api\Request\RequestInterface;
use FiveLab\Component\ObjectMapper\Annotation as DataMapping;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @DataMapping\Object(allProperties=true)
 */
class TypeSearchRequest implements RequestInterface
{
    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @Assert\Choice({"hierarchical", "inline"})
     */
    private $mode = 'hierarchical';

    /**
     * Is inline mode
     *
     * @return bool
     */
    public function isInlineMode()
    {
        return $this->mode == 'inline';
    }

    /**
     * Is hierarchical mode
     *
     * @return bool
     */
    public function isHierarchicalMode()
    {
        return $this->mode == 'hierarchical';
    }
}
