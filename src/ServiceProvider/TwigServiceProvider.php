<?php

namespace AgentPlus\ServiceProvider;

use AgentPlus\AppKernel;
use FiveLab\Component\Exception\UnexpectedTypeException;
use Silex\Application;
use Silex\ServiceProviderInterface;
use Silex\Provider\TwigServiceProvider as SilexTwigServiceProvider;

class TwigServiceProvider implements ServiceProviderInterface
{
    /**
     * {@inheritDoc}
     */
    public function register(Application $app)
    {
        if (!$app instanceof AppKernel) {
            throw UnexpectedTypeException::create($app, AppKernel::class);
        }

        $app->register(new SilexTwigServiceProvider(), [
            'twig.path' => realpath(__DIR__ . '/../Resources/views'),
            'twig.options' => [
                'cache' => $app['kernel.cache_dir'] . '/twig'
            ]
        ]);

        $app['twig'] = $app->share($app->extend('twig', function (\Twig_Environment $twig) {
            return $twig;
        }));
    }

    /**
     * {@inheritDoc}
     */
    public function boot(Application $app)
    {
    }
}
