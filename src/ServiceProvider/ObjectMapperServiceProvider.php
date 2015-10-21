<?php

namespace AgentPlus\ServiceProvider;

use AgentPlus\AppKernel;
use FiveLab\Component\Exception\UnexpectedTypeException;
use FiveLab\Component\ObjectMapper\Metadata\CachedMetadataFactory;
use FiveLab\Component\ObjectMapper\Metadata\Loader\AnnotationLoader;
use FiveLab\Component\ObjectMapper\Metadata\Loader\ChainLoader;
use FiveLab\Component\ObjectMapper\Metadata\MetadataFactory;
use FiveLab\Component\ObjectMapper\ObjectMapper;
use FiveLab\Component\ObjectMapper\Strategy\StrategyRegistry;
use Silex\Application;
use Silex\ServiceProviderInterface;

class ObjectMapperServiceProvider implements ServiceProviderInterface
{
    /**
     * {@inheritDoc}
     */
    public function register(Application $app)
    {
        if (!$app instanceof AppKernel) {
            throw UnexpectedTypeException::create($app, AppKernel::class);
        }

        $app['object_mapper'] = $app->share(function (AppKernel $kernel) {
            $chainLoader = new ChainLoader();
            $chainLoader->addLoader(new AnnotationLoader($kernel->getAnnotationReader()));
            $metadataFactory = new MetadataFactory($chainLoader);
            $strategyRegistry = StrategyRegistry::createDefault();
            $cachedMetadataFactory = new CachedMetadataFactory($metadataFactory, $kernel->getCache());

            return new ObjectMapper($cachedMetadataFactory, $strategyRegistry);
        });
    }

    /**
     * {@inheritDoc}
     */
    public function boot(Application $app)
    {
    }
}
