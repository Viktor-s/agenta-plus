<?php

namespace AgentPlus\Query\Executor;

/**
 * All query executors should implement this interface
 *
 * @internal You can not use interface in another components.
 *           And all query executors should marked as @internal!
 */
interface QueryExecutorInterface
{
    /**
     * Execute query
     *
     * @param object $query
     *
     * @return mixed
     */
    public function execute($query);
}
