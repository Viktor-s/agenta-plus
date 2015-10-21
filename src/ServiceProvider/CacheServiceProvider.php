<?php

namespace AgentPlus\ServiceProvider;

use AgentPlus\AppKernel;
use FiveLab\Component\Cache\ArrayCache;
use FiveLab\Component\Cache\CacheClearer;
use FiveLab\Component\Exception\UnexpectedTypeException;
use Silex\Application;
use Silex\ServiceProviderInterface;
use Symfony\Component\HttpKernel\CacheClearer\ChainCacheClearer;

class CacheServiceProvider implements ServiceProviderInterface
{
    /**
     * {@inheritDoc}
     */
    public function register(Application $app)
    {
        if (!$app instanceof AppKernel) {
            throw UnexpectedTypeException::create($app, AppKernel::class);
        }

        $app['cache.array'] = $app->share(function (){
            return new ArrayCache();
        });

        $app['cache'] = $app->share(function (AppKernel $kernel) {
            return $kernel['cache.array'];
        });

        $app['cache.clearer'] = $app->share(function (AppKernel $kernel) {
            $cacheClearer = new ChainCacheClearer([
                new CacheClearer($kernel['cache'])
            ]);

            return $cacheClearer;
        });
    }

    /**
     * {@inheritDoc}
     */
    public function boot(Application $app)
    {
    }
}
