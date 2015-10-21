<?php

namespace AgentPlus\Api\Error;

use AgentPlus\Exception\Team\TeamNotFoundException;
use FiveLab\Component\Error\ErrorFactoryInterface;

final class TeamErrorFactory implements ErrorFactoryInterface
{
    const TEAM_NOT_FOUND = 80;

    /**
     * {@inheritDoc}
     */
    public function getErrors()
    {
        return [
            self::TEAM_NOT_FOUND => 'Team not found.'
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function getExceptions()
    {
        return [
            TeamNotFoundException::class => self::TEAM_NOT_FOUND
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function getReservedDiapason()
    {
        return [80, 99];
    }
}
