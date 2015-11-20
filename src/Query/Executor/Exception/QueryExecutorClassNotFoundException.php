<?php

namespace AgentPlus\Query\Executor\Exception;

class QueryExecutorClassNotFoundException extends \Exception
{
    /**
     * Create a new exception instance via class
     *
     * @param string $queryClass
     * @param array  $executorClasses
     *
     * @return QueryExecutorClassNotFoundException
     */
    public static function withClasses($queryClass, array $executorClasses)
    {
        $message = sprintf(
            'Not found executor classes "%s" for query "%s".',
            implode(', ', $executorClasses),
            $queryClass
        );

        return new static($message);
    }
}
