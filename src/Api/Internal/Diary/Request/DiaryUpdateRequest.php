<?php

namespace AgentPlus\Api\Internal\Diary\Request;

use FiveLab\Component\ObjectMapper\Annotation as DataMapping;

class DiaryUpdateRequest extends DiaryActionRequest
{
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
}
