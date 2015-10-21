<?php

namespace AgentPlus\ServiceProvider;

use AgentPlus\AppKernel;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\CachedReader;
use FiveLab\Component\Cache\DoctrineCache;
use FiveLab\Component\Exception\UnexpectedTypeException;
use Silex\Application;
use Silex\ServiceProviderInterface;

class AnnotationReaderServiceProvider implements ServiceProviderInterface
{
    /**
     * {@inheritDoc}
     */
    public function register(Application $app)
    {
        if (!$app instanceof AppKernel) {
            throw UnexpectedTypeException::create($app, AppKernel::class);
        }

        $app['annotation_reader'] = $app->share(function (AppKernel $kernel) {
            $annotationReader = new AnnotationReader();
            $cache = new DoctrineCache($kernel->getCache());

            return new CachedReader($annotationReader, $cache);
        });
    }

    /**
     * {@inheritDoc}
     */
    public function boot(Application $app)
    {
    }
}
