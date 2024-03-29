<?php

namespace AgentPlus\Controller\Cabinet;

use Silex\Application;
use Silex\ControllerCollection;
use Silex\ControllerProviderInterface;

class ControllerProvider implements ControllerProviderInterface
{
    public function connect(Application $app)
    {
        $controllers = new ControllerCollection($app['route_factory']);

        $controllers->get('/', 'controller.cabinet:app');

        $controllers->post('/upload', 'controller.cabinet.uploadable:upload')
            ->bind('cabinet_upload');

        return $controllers;
    }
}
