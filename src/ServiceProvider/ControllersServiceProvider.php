<?php

namespace AgentPlus\ServiceProvider;

use AgentPlus\AppKernel;
use AgentPlus\Controller\Api\ApiController;
use AgentPlus\Controller\Cabinet\CabinetController;
use FiveLab\Component\Exception\UnexpectedTypeException;
use Silex\Application;
use Silex\ServiceProviderInterface;

class ControllersServiceProvider implements ServiceProviderInterface
{
    /**
     * {@inheritDoc}
     */
    public function register(Application $app)
    {
        if (!$app instanceof AppKernel) {
            throw UnexpectedTypeException::create($app, AppKernel::class);
        }

        $app['controller.api'] = $app->share(function (AppKernel $kernel) {
            return new ApiController($kernel->getApiServerRegistry());
        });

        $app['controller.cabinet'] = $app->share(function (AppKernel $kernel) {
            return new CabinetController($kernel->getTwig());
        });
    }

    /**
     * {@inheritDoc}
     */
    public function boot(Application $app)
    {
    }
}
