<?php

namespace AgentPlus\Api\Error;

use AgentPlus\Exception\Order\StageNotFoundException;
use FiveLab\Component\Error\ErrorFactoryInterface;

class StageErrorFactory implements ErrorFactoryInterface
{
    const STAGE_NOT_FOUND = 120;

    /**
     * {@inheritDoc}
     */
    public function getErrors()
    {
        return [
            self::STAGE_NOT_FOUND => 'Stage not found.'
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function getExceptions()
    {
        return [
            StageNotFoundException::class => self::STAGE_NOT_FOUND
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function getReservedDiapason()
    {
        return [120, 129];
    }
}
