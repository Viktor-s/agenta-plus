<?php

namespace AgentPlus\Query;

class DateTimeIntervalQuery
{
    /**
     * @var \DateTime
     */
    private $from;

    /**
     * @var \DateTime
     */
    private $to;

    /**
     * With from
     *
     * @param \DateTime $from
     *
     * @return DateTimeIntervalQuery
     */
    public function withFrom(\DateTime $from = null)
    {
        $this->from = $from;

        return $this;
    }

    /**
     * Has from?
     *
     * @return bool
     */
    public function hasFrom()
    {
        return (bool) $this->from;
    }

    /**
     * Get from
     *
     * @return \DateTime|null
     */
    public function getFrom()
    {
        return $this->from;
    }

    /**
     * With to
     *
     * @param \DateTime $to
     *
     * @return DateTimeIntervalQuery
     */
    public function withTo(\DateTime $to = null)
    {
        $this->to = $to;

        return $this;
    }

    /**
     * Has to?
     *
     * @return bool
     */
    public function hasTo()
    {
        return (bool) $this->to;
    }

    /**
     * Get to
     *
     * @return \DateTime|null
     */
    public function getTo()
    {
        return $this->to;
    }
}
