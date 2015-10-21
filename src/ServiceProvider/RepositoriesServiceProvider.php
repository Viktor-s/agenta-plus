<?php

namespace AgentPlus\ServiceProvider;

use AgentPlus\AppKernel;
use AgentPlus\Repository\TeamRepository;
use AgentPlus\Repository\UserRepository;
use FiveLab\Component\Exception\UnexpectedTypeException;
use Silex\Application;
use Silex\ServiceProviderInterface;

class RepositoriesServiceProvider implements ServiceProviderInterface
{
    /**
     * {@inheritDoc}
     */
    public function register(Application $app)
    {
        if (!$app instanceof AppKernel) {
            throw UnexpectedTypeException::create($app, AppKernel::class);
        }

        $app['repository.user'] = $app->share(function (AppKernel $kernel) {
            return new UserRepository($kernel->getDbEntityManager());
        });

        $app['repository.team'] = $app->share(function (AppKernel $kernel) {
            return new TeamRepository($kernel->getDbEntityManager());
        });
    }

    /**
     * {@inheritDoc}
     */
    public function boot(Application $app)
    {
    }
}