<?php

namespace AgentPlus\Model\Pagination;

use FiveLab\Component\ModelNormalizer\Annotation as ModelNormalize;

/**
 * Model for default pagination
 *
 * @ModelNormalize\Object(allProperties=true)
 */
class DefaultPagination implements \Iterator, \Countable
{
    /**
     * @var int
     */
    private $page;

    /**
     * @var int
     */
    private $limit;

    /**
     * @var int
     */
    private $totalItems;

    /**
     * @var array|mixed[]
     *
     * @ModelNormalize\Property(shouldNormalize=true)
     */
    private $storage;

    /**
     * Get active page
     *
     * @return int
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * Get limit
     *
     * @return int
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * Get total items
     *
     * @return int
     */
    public function getTotalItems()
    {
        return $this->totalItems;
    }

    /**
     * Get storage
     *
     * @return array|mixed[]
     */
    public function getStorage()
    {
        return $this->storage;
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
