<?php

namespace AgentPlus\Exception\Client;

class ClientNotFoundException extends \Exception
{
    /**
     * Create a new exception instance via client id
     *
     * @param $id
     *
     * @return ClientNotFoundException
     */
    public static function withId($id)
    {
        $message = sprintf(
            'Not found client with id "%s".',
            $id
        );

        return new static($message);
    }
}
