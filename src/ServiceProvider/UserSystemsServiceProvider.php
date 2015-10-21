<?php

namespace AgentPlus\ServiceProvider;

use AgentPlus\AppKernel;
use AgentPlus\Security\Provider\UserProvider;
use AgentPlus\Security\UserPasswordUpdater;
use FiveLab\Component\Exception\UnexpectedTypeException;
use Silex\Application;
use Silex\ServiceProviderInterface;

class UserSystemsServiceProvider implements ServiceProviderInterface
{
    /**
     * {@inheritDoc}
     */
    public function register(Application $app)
    {
        if (!$app instanceof AppKernel) {
            throw UnexpectedTypeException::create($app, AppKernel::class);
        }

        $app['user.password_updater'] = $app->share(function (AppKernel $kernel) {
            return new UserPasswordUpdater($kernel['security.encoder_factory']);
        });

        $app['user.provider'] = $app->share(function (AppKernel $kernel) {
            return new UserProvider($kernel->getUserRepository());
        });
    }

    /**
     * {@inheritDoc}
     */
    public function boot(Application $app)
    {
    }
}
