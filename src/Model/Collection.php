<?php

namespace AgentPlus\Model;

/**
 * Model of collection
 */
class Collection implements \Iterator, \Countable, \ArrayAccess
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
    public function __construct(array $storage = [])
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

    /**
     * {@inheritDoc}
     */
    public function offsetExists($offset)
    {
        return isset($this->storage[$offset]);
    }

    /**
     * {@inheritDoc}
     */
    public function offsetGet($offset)
    {
        return $this->storage[$offset];
    }

    /**
     * {@inheritDoc}
     */
    public function offsetSet($offset, $value)
    {
        if (null === $offset) {
            $this->storage[] = $value;
        } else {
            $this->storage[$offset] = $value;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function offsetUnset($offset)
    {
        unset ($this->storage[$offset]);
    }
}
