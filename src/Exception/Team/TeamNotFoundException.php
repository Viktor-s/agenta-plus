<?php

namespace AgentPlus\Exception\Team;

class TeamNotFoundException extends \Exception
{
    /**
     * Create a new exception instance via team key
     *
     * @param string $key
     *
     * @return TeamNotFoundException
     */
    public static function withKey($key)
    {
        $message = sprintf(
            'Not found team with key "%s".',
            $key
        );

        return new static($message);
    }
}
