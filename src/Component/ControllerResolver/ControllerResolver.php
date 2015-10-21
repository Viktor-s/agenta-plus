<?php

namespace AgentPlus\Component\ControllerResolver;

use Silex\Application;
use Silex\ControllerResolver as SilexControllerResolver;

/**
 * Override base controller resolver
 */
class ControllerResolver extends SilexControllerResolver
{
    /**
     * Construct
     *
     * @param Application $application
     */
    public function __construct(Application $application)
    {
        parent::__construct($application);
    }

    /**
     * {@inheritDoc}
     */
    protected function createController($controller)
    {
        if (false === strpos($controller, ':')) {
            throw new \InvalidArgumentException(sprintf('Unable to find controller "%s".', $controller));
        }

        list($class, $method) = explode(':', $controller, 2);

        if (isset($this->app[$class])) {
            // Controller as service
            return [$this->app[$class], $method];
        }

        return parent::createController($controller);
    }
}
