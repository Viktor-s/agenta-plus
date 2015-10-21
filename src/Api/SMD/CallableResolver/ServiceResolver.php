<?php

namespace AgentPlus\Api\SMD\CallableResolver;

use AgentPlus\Api\SMD\Action\ServiceAction;
use FiveLab\Component\Api\SMD\Action\ActionInterface;
use FiveLab\Component\Api\SMD\CallableResolver\BaseCallable;
use FiveLab\Component\Api\SMD\CallableResolver\CallableResolverInterface;
use FiveLab\Component\Exception\UnexpectedTypeException;
use FiveLab\Component\Reflection\Reflection;
use Silex\Application;

class ServiceResolver implements CallableResolverInterface
{
    /**
     * @var Application
     */
    private $application;

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
     * {@inheritDoc}
     */
    public function isSupported(ActionInterface $action)
    {
        return $action instanceof ServiceAction;
    }

    /**
     * {@inheritDoc}
     */
    public function resolve(ActionInterface $action)
    {
        if (!$action instanceof ServiceAction) {
            throw UnexpectedTypeException::create($action, ServiceAction::class);
        }

        $serviceId = $action->getServiceId();
        $method = $action->getMethod();

        if (!isset($this->application[$serviceId])) {
            throw new \RuntimeException(sprintf(
                'Can not resolve action, because the service "%s" not found.',
                $serviceId
            ));
        }

        $service = $this->application[$serviceId];
        $reflection = Reflection::loadClassReflection($service);
        $method = $reflection->getMethod($method);

        return new BaseCallable($method, $service);
    }
}
