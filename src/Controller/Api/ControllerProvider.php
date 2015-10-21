<?php

namespace AgentPlus\Controller\Api;

use Silex\Application;
use Silex\ControllerCollection;
use Silex\ControllerProviderInterface;

class ControllerProvider implements ControllerProviderInterface
{
    /**
     * {@inheritDoc}
     */
    public function connect(Application $app)
    {
        $controllers = new ControllerCollection($app['route_factory']);

        $controllers
            ->match('/external', 'controller.api:external')
            ->bind('api_external');

        $controllers
            ->match('/internal', 'controller.api:internal')
            ->bind('api_internal');

        return $controllers;
    }
}
