<?php

namespace AgentPlus\Exception\Order;

class OrderNotFoundException extends \Exception
{
    /**
     * Create a new exception instance via id
     *
     * @param string     $id
     * @param int        $code
     * @param \Exception $prev
     *
     * @return OrderNotFoundException
     */
    public static function withId($id, $code = 0, \Exception $prev = null)
    {
        $message = sprintf(
            'Not found order with id "%s".',
            $id
        );

        return new static($message, $code, $prev);
    }
}
