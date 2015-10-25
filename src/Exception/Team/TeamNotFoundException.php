<?php

namespace AgentPlus\Exception\Team;

class TeamNotFoundException extends \Exception
{
    /**
     * Create a new exception instance via team id
     *
     * @param string $id
     *
     * @return TeamNotFoundException
     */
    public static function withId($id)
    {
        $message = sprintf(
            'Not found team with key "%s".',
            $id
        );

        return new static($message);
    }
}
