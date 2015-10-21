<?php

namespace AgentPlus\Component\Validator;

use FiveLab\Component\Cache\CacheInterface as FiveLabCacheInterface;
use Symfony\Component\Validator\Mapping\Cache\CacheInterface;
use Symfony\Component\Validator\Mapping\ClassMetadata;

class Cache implements CacheInterface
{
    /**
     * @var FiveLabCacheInterface
     */
    private $cache;

    /**
     * Construct
     *
     * @param FiveLabCacheInterface $cache
     */
    public function __construct(FiveLabCacheInterface $cache)
    {
        $this->cache = $cache;
    }

    /**
     * {@inheritDoc}
     */
    public function has($class)
    {
        $key = md5($class);

        return $this->cache->has($key);
    }

    /**
     * {@inheritDoc}
     */
    public function read($class)
    {
        $key = md5($class);

        return $this->cache->get($key) ?: false;
    }

    /**
     * {@inheritDoc}
     */
    public function write(ClassMetadata $metadata)
    {
        $key = md5($metadata->getClassName());

        $this->cache->set($key, $metadata);
    }
}
