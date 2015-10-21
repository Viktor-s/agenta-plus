<?php

namespace AgentPlus\Api\Error;

use FiveLab\Component\Error\ErrorFactoryInterface;

final class UserErrorFactory implements ErrorFactoryInterface
{
    /**
     * {@inheritDoc}
     */
    public function getErrors()
    {
        return [];
    }

    /**
     * {@inheritDoc}
     */
    public function getExceptions()
    {
        return [];
    }

    /**
     * {@inheritDoc}
     */
    public function getReservedDiapason()
    {
        return [50, 79];
    }
}
