<?php

namespace AgentPlus\Controller\Api;

use FiveLab\Component\Api\Server\ServerRegistryInterface;
use Symfony\Component\HttpFoundation\Request;

class ApiController
{
    /**
     * @var ServerRegistryInterface
     */
    private $serverRegistry;

    /**
     * Construct
     *
     * @param ServerRegistryInterface $serverRegistry
     */
    public function __construct(ServerRegistryInterface $serverRegistry)
    {
        $this->serverRegistry = $serverRegistry;
    }

    /**
     * External API
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function external(Request $request)
    {
        return $this->serverRegistry->getServer('external')
            ->handle($request);
    }

    /**
     * Internal API
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function internal(Request $request)
    {
        return $this->serverRegistry->getServer('internal')
            ->handle($request);
    }
}
