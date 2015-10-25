<?php

namespace AgentPlus\Api\Error;

use AgentPlus\Exception\Client\ClientNotFoundException;
use FiveLab\Component\Error\ErrorFactoryInterface;

class ClientErrorFactory implements ErrorFactoryInterface
{
    const CLIENT_NOT_FOUND = 100;

    /**
     * {@inheritDoc}
     */
    public function getErrors()
    {
        return [
            self::CLIENT_NOT_FOUND => 'Client not found.'
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function getExceptions()
    {
        return [
            ClientNotFoundException::class => self::CLIENT_NOT_FOUND
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function getReservedDiapason()
    {
        return [100, 119];
    }
}
