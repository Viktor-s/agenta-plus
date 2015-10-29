<?php

namespace AgentPlus\Exception\Order;

class StageNotFoundException extends \Exception
{
    /**
     * Create a new exception instance with stage id
     *
     * @param string $id
     *
     * @return StageNotFoundException
     */
    public static function withId($id)
    {
        $message = sprintf(
            'Not found stage with id "%s".',
            $id
        );

        return new static($message);
    }
}
