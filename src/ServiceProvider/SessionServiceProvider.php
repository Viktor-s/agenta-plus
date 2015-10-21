<?php

namespace AgentPlus\ServiceProvider;

use AgentPlus\AppKernel;
use FiveLab\Component\Exception\UnexpectedTypeException;
use Silex\Application;
use Silex\ServiceProviderInterface;
use Silex\Provider\SessionServiceProvider as SilexSessionProvider;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\NativeSessionHandler;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\NullSessionHandler;

class SessionServiceProvider implements ServiceProviderInterface
{
    /**
     * {@inheritDoc}
     */
    public function register(Application $app)
    {
        if (!$app instanceof AppKernel) {
            throw UnexpectedTypeException::create($app, AppKernel::class);
        }

        $app->register(new SilexSessionProvider(), [
            'session.storage.handler' => $app->share(function (AppKernel $kernel) {
                $request = $kernel->getRequest();

                if ($kernel->getApiRequestMatcher()->matches($request)) {
                    return new NullSessionHandler();
                } else {
                    return new NativeSessionHandler();
                }
            })
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function boot(Application $app)
    {
    }
}
