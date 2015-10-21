<?php

namespace AgentPlus\Api\Error;

use FiveLab\Component\Error\ErrorFactoryInterface;
use FiveLab\Component\Exception\ViolationListException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

final class SystemErrorFactory implements ErrorFactoryInterface
{
    const SYSTEM_ERROR      = 1;
    const ACCESS_DENIED     = 2;
    const REQUEST_NOT_VALID = 3;

    /**
     * {@inheritDoc}
     */
    public function getErrors()
    {
        return [
            self::SYSTEM_ERROR => 'System error.',
            self::ACCESS_DENIED => 'Access denied.',
            self::REQUEST_NOT_VALID => 'Request not valid.',
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function getExceptions()
    {
        return [
            ViolationListException::class => self::REQUEST_NOT_VALID,
            AccessDeniedException::class => self::ACCESS_DENIED
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function getReservedDiapason()
    {
        return [0, 49];
    }
}
