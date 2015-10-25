<?php

namespace AgentPlus\Exception\Factory;

class FactoryNotFoundException extends \Exception
{
    /**
     * Create a new exception instance via client id
     *
     * @param $id
     *
     * @return FactoryNotFoundException
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
