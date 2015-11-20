<?php

namespace AgentPlus\Query\User;

class SearchUsersByIdsQuery
{
    /**
     * @var array
     */
    private $ids;

    /**
     * Construct
     *
     * @param array $ids
     */
    public function __construct(array $ids)
    {
        $this->ids = $ids;
    }

    /**
     * Get ids
     *
     * @return array
     */
    public function getIds()
    {
        return $this->ids;
    }
}
