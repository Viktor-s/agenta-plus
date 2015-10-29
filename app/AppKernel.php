<?php

namespace AgentPlus;

use AgentPlus\Component\ControllerResolver\ControllerResolver;
use AgentPlus\EventListener\Http\AddAllowOriginSubscriber;
use AgentPlus\EventListener\Kernel\TimeAndMemoryDebugSubscriber;
use AgentPlus\EventListener\Kernel\TimezoneSubscriber;
use AgentPlus\ServiceProvider\AnnotationReaderServiceProvider;
use AgentPlus\ServiceProvider\ApiServiceProvider;
use AgentPlus\ServiceProvider\CacheServiceProvider;
use AgentPlus\ServiceProvider\ControllersServiceProvider;
use AgentPlus\ServiceProvider\DbLayerServiceProvider;
use AgentPlus\ServiceProvider\LoggerServiceProvider;
use AgentPlus\ServiceProvider\ModelNormalizerServiceProvider;
use AgentPlus\ServiceProvider\ModelTransformerServiceProvider;
use AgentPlus\ServiceProvider\ObjectMapperServiceProvider;
use AgentPlus\ServiceProvider\RepositoriesServiceProvider;
use AgentPlus\ServiceProvider\RequestMatcherServiceProvider;
use AgentPlus\ServiceProvider\SecurityServiceProvider;
use AgentPlus\ServiceProvider\TwigServiceProvider;
use AgentPlus\ServiceProvider\UploaderServiceProvider;
use AgentPlus\ServiceProvider\UserSystemsServiceProvider;
use AgentPlus\ServiceProvider\ValidatorServiceProvider;
use Silex\Application;
use Silex\ControllerCollection;
use Silex\ControllerProviderInterface;
use Silex\Provider\SessionServiceProvider;
use Silex\Provider\UrlGeneratorServiceProvider;
use Symfony\Component\Debug\Debug;

class AppKernel extends Application
{
    /**
     * Construct
     *
     * @param string $environment
     * @param bool   $debug
     */
    public function __construct($environment, $debug)
    {
        if ($debug) {
            Debug::enable();
        } else {
            error_reporting(0);
        }

        $configs = $this->includeConfig($environment);

        if ($debug) {
            $configs['assets_version'] = time();
        }

        parent::__construct(array_merge($configs, [
            'debug' => $debug,
            'kernel.root_dir' => __DIR__,
            'kernel.environment' => $environment,
            'kernel.cache_dir' => __DIR__ . '/../var/cache/' . $environment,
            'kernel.logs_dir' => __DIR__ . '/../var/logs'
        ]));

        // Register providers
        $this->registerProvidersAndServices();

        // Register error handlers
        $this->registerErrorHandlers();

        // Register subscribers
        $this->registerEventSubscribers();

        // Configure routing
        $this->configureRouting();
    }

    /**
     * Override mount method for use "host" parameter
     *
     * @param string                                           $prefix
     * @param ControllerProviderInterface|ControllerCollection $controllers
     * @param string                                           $host
     *
     * @return AppKernel
     */
    public function mount($prefix, $controllers, $host = null)
    {
        if ($controllers instanceof ControllerProviderInterface) {
            $connectedControllers = $controllers->connect($this);

            if (!$connectedControllers instanceof ControllerCollection) {
                throw new \LogicException(sprintf(
                    'The method "%s::connect" must return a "ControllerCollection" instance. Got: "%s"',
                    get_class($controllers),
                    is_object($connectedControllers) ? get_class($connectedControllers) : gettype($connectedControllers)
                ));
            }

            $controllers = $connectedControllers;
        } elseif (!$controllers instanceof ControllerCollection) {
            throw new \LogicException('The "mount" method takes either a "ControllerCollection" or a "ControllerProviderInterface" instance.');
        }

        if ($host) {
            /** @var \Silex\Route $controllers */
            $controllers->host($host);
        }

        $this['controllers']->mount($prefix, $controllers);

        return $this;
    }

    /**
     * Is debug
     *
     * @return bool
     */
    public function isDebug()
    {
        return $this['debug'];
    }

    /**
     * Get environment
     *
     * @return string
     */
    public function getEnvironment()
    {
        return $this['kernel.environment'];
    }

    /**
     * Get kernel dir
     *
     * @return string
     */
    public function getRootDir()
    {
        return $this['kernel.root_dir'];
    }

    /**
     * Get cache directory
     *
     * @return string
     */
    public function getCacheDir()
    {
        return $this['kernel.cache_dir'];
    }

    /**
     * Get logs directory
     *
     * @return string
     */
    public function getLogsDir()
    {
        return $this['kernel.logs_dir'];
    }

    /**
     * Get event dispatcher
     *
     * @return \Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    public function getEventDispatcher()
    {
        return $this['dispatcher'];
    }

    /**
     * Get cache
     *
     * @return \FiveLab\Component\Cache\CacheInterface
     */
    public function getCache()
    {
        return $this['cache'];
    }

    /**
     * Get cache clearer
     *
     * @return \Symfony\Component\HttpKernel\CacheClearer\CacheClearerInterface
     */
    public function getCacheClearer()
    {
        return $this['cache.clearer'];
    }

    /**
     * Get annotation reader
     *
     * @return \Doctrine\Common\Annotations\Reader|\Doctrine\Common\Annotations\AnnotationReader
     */
    public function getAnnotationReader()
    {
        return $this['annotation_reader'];
    }

    /**
     * Get request
     *
     * @return \Symfony\Component\HttpFoundation\Request
     */
    public function getRequest()
    {
        return $this['request'];
    }

    /**
     * Get request stack
     *
     * @return \Symfony\Component\HttpFoundation\RequestStack
     */
    public function getRequestStack()
    {
        return $this['request_stack'];
    }

    /**
     * Get API request matcher
     *
     * @return \Symfony\Component\HttpFoundation\RequestMatcher
     */
    public function getApiRequestMatcher()
    {
        return $this['request_matcher.api'];
    }

    /**
     * Get cabinet request matcher
     *
     * @return \Symfony\Component\HttpFoundation\RequestMatcher
     */
    public function getCabinetRequestMatcher()
    {
        return $this['request_matcher.cabinet'];
    }

    /**
     * Get API Internal request matcher
     *
     * @return \Symfony\Component\HttpFoundation\RequestMatcher
     */
    public function getApiInternalRequestMatcher()
    {
        return $this['request_matcher.api_internal'];
    }

    /**
     * Get API External request matcher
     *
     * @return \Symfony\Component\HttpFoundation\RequestMatcher
     */
    public function getApiExternalRequestMatcher()
    {
        return $this['request_matcher.api_external'];
    }

    /**
     * Get logger
     *
     * @return \Psr\Log\LoggerInterface
     */
    public function getLogger()
    {
        return $this['logger'];
    }

    /**
     * Get object mapper
     *
     * @return \FiveLab\Component\ObjectMapper\ObjectMapperInterface
     */
    public function getObjectMapper()
    {
        return $this['object_mapper'];
    }

    /**
     * Get model transformer
     *
     * @return \FiveLab\Component\ModelTransformer\ModelTransformerManagerInterface
     */
    public function getModelTransformer()
    {
        return $this['model_transformer'];
    }

    /**
     * Get model normalizer
     *
     * @return \FiveLab\Component\ModelNormalizer\ModelNormalizerManagerInterface
     */
    public function getModelNormalizer()
    {
        return $this['model_normalizer'];
    }

    /**
     * Get DB connection
     *
     * @return \Doctrine\DBAL\Connection
     */
    public function getDbConnection()
    {
        return $this['db'];
    }

    /**
     * Get database entity manager
     *
     * @return \Doctrine\ORM\EntityManagerInterface
     */
    public function getDbEntityManager()
    {
        return $this['db.orm.em'];
    }

    /**
     * Get DB migrations schema provider
     *
     * @return \Doctrine\DBAL\Migrations\Provider\SchemaProviderInterface
     */
    public function getDbMigrationsSchemaProvider()
    {
        return $this['db.orm.migrations.schema_provider'];
    }

    /**
     * Get DB migrations configuration
     *
     * @return \Doctrine\DBAL\Migrations\Configuration\Configuration
     */
    public function getDbMigrationsConfiguration()
    {
        return $this['db.orm.migrations.configuration'];
    }

    /**
     * Get DB fixtures loader
     *
     * @return \AgentPlus\Component\Doctrine\DataFixtures\Loader
     */
    public function getDbOrmFixturesLoader()
    {
        return $this['db.orm.fixtures.loader'];
    }

    /**
     * Get DB fixtures purger
     *
     * @return \Doctrine\Common\DataFixtures\Executor\ORMExecutor
     */
    public function getDbOrmFixturesExecutor()
    {
        return $this['db.orm.fixtures.executor'];
    }

    /**
     * Get ORM Transactional
     *
     * @return \FiveLab\Component\Transactional\DoctrineORMTransactional
     */
    public function getOrmTransactional()
    {
        return $this['db.orm.transactional'];
    }

    /**
     * Get user repository
     *
     * @return \AgentPlus\Repository\UserRepository
     */
    public function getUserRepository()
    {
        return $this['repository.user'];
    }

    /**
     * Get team repository
     *
     * @return \AgentPlus\Repository\TeamRepository
     */
    public function getTeamRepository()
    {
        return $this['repository.team'];
    }

    /**
     * Get client repository
     *
     * @return \AgentPlus\Repository\ClientRepository
     */
    public function getClientRepository()
    {
        return $this['repository.client'];
    }

    /**
     * Get factory repository
     *
     * @return \AgentPlus\Repository\FactoryRepository
     */
    public function getFactoryRepository()
    {
        return $this['repository.factory'];
    }

    /**
     * Get diary repository
     *
     * @return \AgentPlus\Repository\DiaryRepository
     */
    public function getDiaryRepository()
    {
        return $this['repository.diary'];
    }

    /**
     * Get stage repository
     *
     * @return \AgentPlus\Repository\StageRepository
     */
    public function getStageRepository()
    {
        return $this['repository.stage'];
    }

    /**
     * Get currency repository
     *
     * @return \AgentPlus\Repository\CurrencyRepository
     */
    public function getCurrencyRepository()
    {
        return $this['repository.currency'];
    }

    /**
     * Get validator
     *
     * @return \AgentPlus\Component\Validator\Validator
     */
    public function getValidator()
    {
        return $this['validator'];
    }

    /**
     * Get twig
     *
     * @return \Twig_Environment
     */
    public function getTwig()
    {
        return $this['twig'];
    }

    /**
     * Get API server registry
     *
     * @return \AgentPlus\Api\ServerRegistry
     */
    public function getApiServerRegistry()
    {
        return $this['api.server_registry'];
    }

    /**
     * Get user password updater
     *
     * @return \AgentPlus\Security\UserPasswordUpdater
     */
    public function getUserPasswordUpdater()
    {
        return $this['user.password_updater'];
    }

    /**
     * Get security token storage
     *
     * @return \Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface
     */
    public function getSecurityTokenStorage()
    {
        return $this['security.token_storage'];
    }

    /**
     * Get security authorization checker
     *
     * @return \Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface
     */
    public function getSecurityAuthorizationChecker()
    {
        return $this['security.authorization_checker'];
    }

    /**
     * Get uploader
     *
     * @return \AgentPlus\Component\Uploader\Uploader
     */
    public function getUploader()
    {
        return $this['uploader'];
    }

    /**
     * Include configuration
     *
     * @param string $environment
     *
     * @return array
     */
    private function includeConfig($environment)
    {
        $configFile = __DIR__ . '/config.php';

        if (!file_exists($configFile) || !is_file($configFile)) {
            throw new \RuntimeException('Not found configuration file "app/config.php".');
        }

        $config = include $configFile;

        if (!is_array($config)) {
            throw new \RuntimeException('Invalid configuration.');
        }

        // Include environment configuration
        $envConfigFile = __DIR__ . '/config/' . $environment . '.php';

        if (file_exists($envConfigFile) && is_file($configFile)) {
            $envConfig = include $envConfigFile;

            if (!$envConfig || !is_array($config)) {
                throw new \RuntimeException(sprintf(
                    'Invalid configuration for environment "%s".',
                    $environment
                ));
            }

            $config = array_merge($config, $envConfig);
        }

        return $config;
    }

    /**
     * Register providers
     */
    private function registerProvidersAndServices()
    {
        $this->register(new LoggerServiceProvider());
        $this->register(new CacheServiceProvider());
        $this->register(new SessionServiceProvider());
        $this->register(new RequestMatcherServiceProvider());
        $this->register(new SecurityServiceProvider());
        $this->register(new DbLayerServiceProvider());
        $this->register(new RepositoriesServiceProvider());
        $this->register(new ObjectMapperServiceProvider());
        $this->register(new AnnotationReaderServiceProvider());
        $this->register(new UrlGeneratorServiceProvider());
        $this->register(new ValidatorServiceProvider());
        $this->register(new ModelTransformerServiceProvider());
        $this->register(new ModelNormalizerServiceProvider());

        $this->register(new TwigServiceProvider());
        $this->register(new UserSystemsServiceProvider());
        $this->register(new UploaderServiceProvider());

        $this->register(new ControllersServiceProvider());
        $this->register(new ApiServiceProvider());

        // Rewrite controller resolver
        $this['resolver'] = $this->share(function () {
            return new ControllerResolver($this, $this['logger']);
        });
    }

    /**
     * Configure routing
     */
    private function configureRouting()
    {
        include_once __DIR__ . '/routing.php';
    }

    /**
     * Register error handlers
     */
    private function registerErrorHandlers()
    {
    }

    /**
     * Register event subscribers
     */
    private function registerEventSubscribers()
    {
        $dispatcher = $this->getEventDispatcher();

        $dispatcher->addSubscriber(new TimeAndMemoryDebugSubscriber());
        $dispatcher->addSubscriber(new TimezoneSubscriber());
    }
}