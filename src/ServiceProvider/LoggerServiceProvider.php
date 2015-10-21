<?php

namespace AgentPlus\ServiceProvider;

use AgentPlus\AppKernel;
use FiveLab\Component\Exception\UnexpectedTypeException;
use Monolog\Logger;
use Silex\Application;
use Silex\Provider\MonologServiceProvider;
use Silex\ServiceProviderInterface;

class LoggerServiceProvider implements ServiceProviderInterface
{
    /**
     * {@inheritDoc}
     */
    public function register(Application $app)
    {
        if (!$app instanceof AppKernel) {
            throw UnexpectedTypeException::create($app, AppKernel::class);
        }

        $app->register(new MonologServiceProvider(), [
            'monolog.name' => 'app',
            'monolog.level' => $app->isDebug() ? Logger::DEBUG : Logger::WARNING,
            'monolog.logfile' => $app->getLogsDir() . '/' . $app->getEnvironment() . '.log'
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function boot(Application $app)
    {
    }
}
