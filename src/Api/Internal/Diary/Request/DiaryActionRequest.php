<?php

namespace AgentPlus\Api\Internal\Diary\Request;

use FiveLab\Component\Api\Request\RequestInterface;
use FiveLab\Component\ObjectMapper\Annotation as DataMapping;
use Symfony\Component\Validator\Constraints as Assert;

class DiaryActionRequest implements RequestInterface
{
    /**
     * The diary identifier
     *
     * @var string
     *
     * @DataMapping\Property()
     *
     * @Assert\NotBlank
     */
    protected $id;

    /**
     * Get id
     *
     * @return string
     */
    public function getDiaryId()
    {
        return $this->id;
    }
}
