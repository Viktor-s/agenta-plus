<?php

namespace AgentPlus\Exception\Catalog;

class CatalogNotFoundException extends \Exception
{
    /**
     * Create a new exception instance via id
     *
     * @param string $id
     *
     * @return CatalogNotFoundException
     */
    public static function withId($id)
    {
        $message = sprintf(
            'Not found catalog with id "%s".',
            $id
        );

        return new static($message);
    }
}
