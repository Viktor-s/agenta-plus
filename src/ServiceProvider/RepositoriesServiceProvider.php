<?php

namespace AgentPlus\ServiceProvider;

use AgentPlus\AppKernel;
use AgentPlus\Repository\ClientRepository;
use AgentPlus\Repository\CurrencyRepository;
use AgentPlus\Repository\DiaryRepository;
use AgentPlus\Repository\FactoryRepository;
use AgentPlus\Repository\StageRepository;
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

        $app['repository.client'] = $app->share(function (AppKernel $kernel) {
            return new ClientRepository($kernel->getDbEntityManager());
        });

        $app['repository.factory'] = $app->share(function (AppKernel $kernel) {
            return new FactoryRepository($kernel->getDbEntityManager());
        });

        $app['repository.diary'] = $app->share(function (AppKernel $kernel) {
            return new DiaryRepository($kernel->getDbEntityManager());
        });

        $app['repository.stage'] = $app->share(function (AppKernel $kernel) {
            return new StageRepository($kernel->getDbEntityManager());
        });

        $app['repository.currency'] = $app->share(function (AppKernel $kernel) {
            return new CurrencyRepository($kernel->getDbEntityManager());
        });
    }

    /**
     * {@inheritDoc}
     */
    public function boot(Application $app)
    {
    }
}
