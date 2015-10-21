<?php

namespace AgentPlus\Api;

use FiveLab\Component\Api\Server\Exception\ServerNotFoundException;
use FiveLab\Component\Api\Server\ServerRegistryInterface;
use Silex\Application;

class ServerRegistry implements ServerRegistryInterface
{
    /**
     * @var Application
     */
    private $application;

    /**
     * @var array
     */
    private $servers = [];

    /**
     * Construct
     *
     * @param Application $application
     */
    public function __construct(Application $application)
    {
        $this->application = $application;
    }

    /**
     * Add server to registry
     *
     * @param string $key
     * @param string $serviceId
     *
     * @return ServerRegistry
     */
    public function addServer($key, $serviceId)
    {
        $this->servers[$key] = $serviceId;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getServer($key)
    {
        if (!isset($this->servers[$key])) {
            throw new ServerNotFoundException(sprintf(
                'Not found server with key "%s".',
                $key
            ));
        }

        $serviceId = $this->servers[$key];

        if (!isset($this->application[$serviceId])) {
            throw new ServerNotFoundException(sprintf(
                'Not found service "%s" for server "%s".',
                $serviceId,
                $key
            ));
        }

        return $this->application[$serviceId];
    }
}
