<?php

namespace AgentPlus\Exception\Currency;

class CurrencyNotFoundException extends \Exception
{
    /**
     * Create a new exception instance via currency code
     *
     * @param string $code
     *
     * @return CurrencyNotFoundException
     */
    public static function withCode($code)
    {
        $message = sprintf(
            'Not found currency with code "%s".',
            $code
        );

        return new static($message);
    }
}
