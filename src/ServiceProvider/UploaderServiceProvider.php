<?php

namespace AgentPlus\ServiceProvider;

use AgentPlus\AppKernel;
use AgentPlus\Component\Uploader\Uploader;
use FiveLab\Component\Exception\UnexpectedTypeException;
use Silex\Application;
use Silex\ServiceProviderInterface;

class UploaderServiceProvider implements ServiceProviderInterface
{
    /**
     * {@inheritDoc}
     */
    public function register(Application $app)
    {
        if (!$app instanceof AppKernel) {
            throw UnexpectedTypeException::create($app, AppKernel::class);
        }

        $app['uploader'] = $app->share(function (AppKernel $kernel) {
            $webPath = realpath($kernel->getRootDir() . '/../web');
            $uploadsPath = '/uploads';
            //$tmpPath = sys_get_temp_dir() . '/agenta-plus/uploads';
            $tmpPath = $kernel->getRootDir() . '/../var/tmp';

            if (!is_dir($tmpPath)) {
                // Try create temp path
                mkdir($tmpPath, 0775, true);
            }

            return new Uploader($webPath, $uploadsPath, realpath($tmpPath));
        });
    }

    /**
     * {@inheritDoc}
     */
    public function boot(Application $app)
    {
    }
}
