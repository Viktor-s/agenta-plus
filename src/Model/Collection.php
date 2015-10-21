<?php

namespace AgentPlus\Model;

/**
 * Model of collection
 */
class Collection implements \Iterator, \Countable
{
    /**
     * @var array|mixed[]
     */
    private $storage;

    /**
     * Construct
     *
     * @param array $storage
     */
    public function __construct(array $storage)
    {
        $this->storage = $storage;
    }

    /**
     * {@inheritDoc}
     */
    public function current()
    {
        return current($this->storage);
    }

    /**
     * {@inheritDoc}
     */
    public function next()
    {
        return next($this->storage);
    }

    /**
     * {@inheritDoc}
     */
    public function key()
    {
        return key($this->storage);
    }

    /**
     * {@inheritDoc}
     */
    public function valid()
    {
        return key($this->storage) !== null;
    }

    /**
     * {@inheritDoc}
     */
    public function rewind()
    {
        reset($this->storage);
    }

    /**
     * {@inheritDoc}
     */
    public function count()
    {
        return count($this->storage);
    }
}
