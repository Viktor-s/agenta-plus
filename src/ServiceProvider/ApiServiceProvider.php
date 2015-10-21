<?php

namespace AgentPlus\ServiceProvider;

use AgentPlus\Api\Error\SystemErrorFactory;
use AgentPlus\Api\Error\TeamErrorFactory;
use AgentPlus\Api\Error\UserErrorFactory;
use AgentPlus\Api\External\ExternalApi;
use AgentPlus\Api\Internal\InternalApi;
use AgentPlus\Api\Internal\Team\TeamApi;
use AgentPlus\Api\ServerRegistry;
use AgentPlus\Api\SMD\CallableResolver\ServiceResolver;
use AgentPlus\Api\SMD\Loader\ServiceAnnotatedLoader;
use AgentPlus\AppKernel;
use FiveLab\Component\Api\Doc\DocGenerator;
use FiveLab\Component\Api\Doc\Formatter\FormatterRegistry;
use FiveLab\Component\Api\EventListener\LoggerSubscriber;
use FiveLab\Component\Api\EventListener\ResponseTransformableSubscriber;
use FiveLab\Component\Api\Handler\HandlerInterface;
use FiveLab\Component\Api\Handler\Builder\HandlerBuilder;
use FiveLab\Component\Api\Handler\Parameter\ObjectMapperParameterResolverAndExtractor;
use FiveLab\Component\Api\Response\Response;
use FiveLab\Component\Api\Server\JsonRpc\JsonRpcServer;
use FiveLab\Component\Exception\UnexpectedTypeException;
use Silex\Application;
use Silex\ServiceProviderInterface;

class ApiServiceProvider implements ServiceProviderInterface
{
    /**
     * {@inheritDoc}
     */
    public function register(Application $app)
    {
        if (!$app instanceof AppKernel) {
            throw UnexpectedTypeException::create($app, AppKernel::class);
        }

        $app['api.action.team'] = $app->share(function (AppKernel $kernel) {
            return new TeamApi(
                $kernel->getTeamRepository(),
                $kernel->getOrmTransactional(),
                $kernel->getSecurityTokenStorage(),
                $kernel->getSecurityAuthorizationChecker()
            );
        });

        $app['api.action.internal'] = $app->share(function () {
            return new InternalApi();
        });

        $app['api.action.external'] = $app->share(function (){
            return new ExternalApi();
        });

        $app['api.server.internal'] = $app->share(function (AppKernel $kernel) {
            $annotatedLoader = new ServiceAnnotatedLoader($kernel->getAnnotationReader());
            $annotatedLoader->addService('api.action.internal', InternalApi::class);
            $annotatedLoader->addService('api.action.team', TeamApi::class);

            $builder = new HandlerBuilder();
            $builder
                ->addCallableResolver(new ServiceResolver($kernel))
                ->addActionLoader($annotatedLoader);

            $this->configureBuilder($builder, $kernel);

            // Attention: the "handler" variable proxy to create server!
            $handler = null;
            $callableLoader = $builder->addCallableHandle();
            $callableLoader->addCallable('error.list', function () use (&$handler) {
                /** @var HandlerInterface $handler */
                return new Response($handler->getErrors()->getErrors());
            });

            return $this->createJsonRpcServer($builder, $kernel, $handler);
        });

        $app['api.server.external'] = $app->share(function (AppKernel $kernel) {
            $annotatedLoader = new ServiceAnnotatedLoader($kernel->getAnnotationReader());
            $annotatedLoader->addService('api.action.external', ExternalApi::class);

            $builder = new HandlerBuilder();
            $builder
                ->addCallableResolver(new ServiceResolver($kernel))
                ->addActionLoader($annotatedLoader);

            $this->configureBuilder($builder, $kernel);

            return $this->createJsonRpcServer($builder, $kernel);
        });

        $app['api.server_registry'] = $app->share(function (AppKernel $kernel) {
            $registry = new ServerRegistry($kernel);
            $registry->addServer('external', 'api.server.external');
            $registry->addServer('internal', 'api.server.internal');

            return $registry;
        });
    }

    /**
     * {@inheritDoc}
     */
    public function boot(Application $app)
    {
    }

    /**
     * Configure builder
     *
     * @param HandlerBuilder $builder
     * @param AppKernel      $kernel
     */
    private function configureBuilder(HandlerBuilder $builder, AppKernel $kernel)
    {
        static $parameterResolver = null;
        static $loggerSubscriber = null;
        static $responseTransformableSubscriber = null;

        if (!$parameterResolver) {
            $parameterResolver = new ObjectMapperParameterResolverAndExtractor(
                $kernel->getObjectMapper(),
                $kernel->getValidator(),
                $kernel->getLogger(),
                $kernel->isDebug()
            );
        }

        if (!$loggerSubscriber) {
            $loggerSubscriber = new LoggerSubscriber($kernel->getLogger());
            $kernel->getEventDispatcher()->addSubscriber($loggerSubscriber);
        }

        if (!$responseTransformableSubscriber) {
            $responseTransformableSubscriber = new ResponseTransformableSubscriber(
                $kernel->getModelTransformer(),
                $kernel->getModelNormalizer()
            );
            $kernel->getEventDispatcher()->addSubscriber($responseTransformableSubscriber);
        }

        $builder
            ->setEventDispatcher($kernel->getEventDispatcher())
            ->setLogger($kernel->getLogger())
            ->setParameterResolver($parameterResolver)
            ->addErrorFactory(new SystemErrorFactory())
            ->addErrorFactory(new UserErrorFactory())
            ->addErrorFactory(new TeamErrorFactory());
    }

    /**
     * Create json rpc server
     *
     * @param HandlerBuilder   $builder
     * @param AppKernel        $kernel
     * @param HandlerInterface $handler
     *
     * @return JsonRpcServer
     */
    private function createJsonRpcServer(HandlerBuilder $builder, AppKernel $kernel, HandlerInterface &$handler = null)
    {
        $handler = $builder->buildHandler();
        $docExtractor = $builder->buildDocExtractor();
        $docGenerator = new DocGenerator($docExtractor, FormatterRegistry::createDefault());

        return new JsonRpcServer($handler, $docGenerator, $kernel->isDebug());
    }
}
