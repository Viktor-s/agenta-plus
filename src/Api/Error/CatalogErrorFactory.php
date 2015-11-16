<?php

namespace AgentPlus\Api\Error;

use AgentPlus\Exception\Catalog\CatalogNotFoundException;
use FiveLab\Component\Error\ErrorFactoryInterface;

class CatalogErrorFactory implements ErrorFactoryInterface
{
    const CATALOG_NOT_FOUND = 130;

    /**
     * {@inheritDoc}
     */
    public function getErrors()
    {
        return [
            self::CATALOG_NOT_FOUND => 'Not found catalog.'
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function getExceptions()
    {
        return [
            CatalogNotFoundException::class => self::CATALOG_NOT_FOUND
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function getReservedDiapason()
    {
        return [130, 149];
    }
}
