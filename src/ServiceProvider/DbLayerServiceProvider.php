<?php

namespace AgentPlus\ServiceProvider;

use AgentPlus\AppKernel;
use AgentPlus\Component\Doctrine\DataFixtures\Loader;
use AgentPlus\Component\Doctrine\DBAL\Types\UTCDateTimeType;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\DBAL\Migrations\Provider\OrmSchemaProvider;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Configuration as ORMConfiguration;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Doctrine\DBAL\Migrations\Configuration\Configuration as MigrationsConfiguration;
use FiveLab\Component\Exception\UnexpectedTypeException;
use FiveLab\Component\Transactional\DoctrineORMTransactional;
use Silex\Application;
use Silex\Provider\DoctrineServiceProvider;
use Silex\ServiceProviderInterface;

class DbLayerServiceProvider implements ServiceProviderInterface
{
    /**
     * {@inheritDoc}
     */
    public function register(Application $app)
    {
        if (!$app instanceof AppKernel) {
            throw UnexpectedTypeException::create($app, AppKernel::class);
        }

        // Register Doctrine DBAL
        $app->register(new DoctrineServiceProvider(), [
            'db.options' => [
                'driver' => $app['db.driver'],
                'host' => $app['db.host'],
                'dbname' => $app['db.dbname'],
                'user' => $app['db.user'],
                'password' => $app['db.password']
            ]
        ]);

        // Add custom types for DBAL
        Type::overrideType(Type::DATETIME, UTCDateTimeType::class);

        // Register Doctrine ORM EntityManager
        $app['db.orm.em'] = $app->share(function (AppKernel $kernel) {
            $connection = $kernel->getDbConnection();

            $annotationDriver = new AnnotationDriver($kernel->getAnnotationReader(), [
                realpath($kernel->getRootDir() . '/../src/Entity')
            ]);

            $configuration = new ORMConfiguration();
            $configuration->setProxyDir($kernel->getCacheDir() . '/doctrine/Proxy');
            $configuration->setProxyNamespace('__Proxy__');
            $configuration->setAutoGenerateProxyClasses(true);
            $configuration->setMetadataDriverImpl($annotationDriver);
            $configuration->addEntityNamespace('TaleForChild', 'TaleForChild\Entity');

            $em = EntityManager::create($connection, $configuration);

            return $em;
        });

        // Register transactional service
        $app['db.orm.transactional'] = $app->share(function (AppKernel $kernel) {
            return new DoctrineORMTransactional($kernel->getDbEntityManager());
        });

        // Register migrations
        $app['db.orm.migrations.schema_provider'] = $app->share(function (AppKernel $kernel) {
            return new OrmSchemaProvider($kernel->getDbEntityManager());
        });

        $app['db.orm.migrations.configuration'] = $app->share(function (AppKernel $kernel) {
            $configuration = new MigrationsConfiguration($kernel->getDbConnection());

            $dir = $kernel->getRootDir() . '/Migrations/ORM';

            if (!file_exists($dir)) {
                mkdir($dir, 0775, true);
            } else if (!is_dir($dir)) {
                throw new \RuntimeException(sprintf(
                    'The path "%s" is not a directory.',
                    $dir
                ));
            }

            $configuration->setName('AgentPlus');
            $configuration->setMigrationsNamespace('AgentPlus\Migrations');
            $configuration->setMigrationsDirectory($dir);
            $configuration->setMigrationsTableName('migration_versions');

            $configuration->registerMigrationsFromDirectory($dir);

            return $configuration;
        });

        if ($app->isDebug()) {
            // Adds the fixtures
            $app['db.orm.fixtures.loader'] = $app->share(function (AppKernel $kernel) {
                $dir = $kernel->getRootDir() . '/DataFixtures/ORM';

                if (!file_exists($dir)) {
                    mkdir($dir, 0775, true);
                } else if (!is_dir($dir)) {
                    throw new \RuntimeException(sprintf(
                        'The path "%s" is not a directory.',
                        $dir
                    ));
                }

                $loader = new Loader($kernel);
                $loader->loadFromDirectory($dir);

                return $loader;
            });

            $app['db.orm.fixtures.executor'] = $app->share(function (AppKernel $kernel) {
                $purger = new ORMPurger($kernel->getDbEntityManager());
                $purger->setPurgeMode(ORMPurger::PURGE_MODE_TRUNCATE);
                $executor = new ORMExecutor($kernel->getDbEntityManager(), $purger);

                return $executor;
            });
        }
    }

    /**
     * {@inheritDoc}
     */
    public function boot(Application $app)
    {
    }
}
