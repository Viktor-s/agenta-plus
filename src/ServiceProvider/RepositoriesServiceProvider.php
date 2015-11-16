<?php

namespace AgentPlus\ServiceProvider;

use AgentPlus\AppKernel;
use AgentPlus\Repository\CatalogRepository;
use AgentPlus\Repository\ClientRepository;
use AgentPlus\Repository\CurrencyRepository;
use AgentPlus\Repository\DiaryRepository;
use AgentPlus\Repository\FactoryRepository;
use AgentPlus\Repository\OrderRepository;
use AgentPlus\Repository\RepositoryRegistry;
use AgentPlus\Repository\StageRepository;
use AgentPlus\Repository\TeamRepository;
use AgentPlus\Repository\UserRepository;
use FiveLab\Component\Exception\UnexpectedTypeException;
use FiveLab\Component\Reflection\Reflection;
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

        $app['repository.registry'] = $app->share(function () {
            return new RepositoryRegistry();
        });

        $app->extend('repository.registry', function (RepositoryRegistry $registry, AppKernel $kernel) {
            Reflection::setPropertiesValue($registry, [
                'userRepository' => $kernel['repository.user'],
                'teamRepository' => $kernel['repository.team'],
                'clientRepository' => $kernel['repository.client'],
                'factoryRepository' => $kernel['repository.factory'],
                'diaryRepository' => $kernel['repository.diary'],
                'stageRepository' => $kernel['repository.stage'],
                'currencyRepository' => $kernel['repository.currency'],
                'catalogRepository' => new CatalogRepository($kernel->getDbEntityManager()),
                'orderRepository' => new OrderRepository($kernel->getDbEntityManager()),
            ]);

            return $registry;
        });
    }

    /**
     * {@inheritDoc}
     */
    public function boot(Application $app)
    {
    }
}
