<?php

namespace AgentPlus\Api\Request;

use FiveLab\Component\ObjectMapper\Annotation as DataMapper;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Data time interval request model
 *
 * @DataMapper\Object(allProperties=true)
 */
class DateTimeInterval
{
    /**
     * @var string
     *
     * @Assert\DateTime()
     */
    private $from;

    /**
     * @var string
     *
     * @Assert\DateTime()
     */
    private $to;

    /**
     * Get from
     *
     * @return \DateTime|null
     */
    public function getFrom()
    {
        if (!$this->from) {
            return null;
        }

        $date = \DateTime::createFromFormat('Y-m-d H:i:s', $this->from);

        return $date;
    }

    /**
     * Get to
     *
     * @return \DateTime|null
     */
    public function getTo()
    {
        if (!$this->to) {
            return null;
        }

        $date = \DateTime::createFromFormat('Y-m-d H:i:s', $this->to);

        return $date;
    }
}
