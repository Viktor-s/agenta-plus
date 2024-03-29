<?php

namespace AgentPlus\ServiceProvider;

use AgentPlus\AppKernel;
use FiveLab\Component\Exception\UnexpectedTypeException;
use Silex\Application;
use Silex\ServiceProviderInterface;
use Symfony\Component\HttpFoundation\RequestMatcher;

class RequestMatcherServiceProvider implements ServiceProviderInterface
{
    /**
     * {@inheritDoc}
     */
    public function register(Application $app)
    {
        if (!$app instanceof AppKernel) {
            throw UnexpectedTypeException::create($app, AppKernel::class);
        }

        $app['request_matcher.api'] = $app->share(function (AppKernel $kernel) {
            return new RequestMatcher('/api');
        });

        $app['request_matcher.cabinet'] = $app->share(function (AppKernel $kernel) {
            return new RequestMatcher('/', null);
        });

        $app['request_matcher.api_external'] = $app->share(function (AppKernel $kernel) {
            return new RequestMatcher('/api/external', null, ['GET', 'POST']);
        });

        $app['request_matcher.api_internal'] = $app->share(function (AppKernel $kernel) {
            return new RequestMatcher('/api/internal', null, ['GET', 'POST']);
        });
    }

    /**
     * {@inheritDoc}
     */
    public function boot(Application $app)
    {
    }
}
