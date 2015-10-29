<?php

namespace AgentPlus\ServiceProvider;

use AgentPlus\AppKernel;
use AgentPlus\Component\ModelTransformer\ClientModelTransformer;
use AgentPlus\Component\ModelTransformer\DefaultORMPaginationModelTransformer;
use AgentPlus\Component\ModelTransformer\Diary\DiaryModelTransformer;
use AgentPlus\Component\ModelTransformer\Diary\DiaryMoneyModelTransformer;
use AgentPlus\Component\ModelTransformer\TraversableModelTransformer;
use AgentPlus\Component\ModelTransformer\UserModelTransformer;
use FiveLab\Component\Exception\UnexpectedTypeException;
use FiveLab\Component\ModelTransformer\ModelTransformerManager;
use FiveLab\Component\ModelTransformer\Transformer\Annotated\AnnotatedModelTransformer;
use FiveLab\Component\ModelTransformer\Transformer\Annotated\Metadata\CachedMetadataFactory;
use FiveLab\Component\ModelTransformer\Transformer\Annotated\Metadata\MetadataFactory;
use FiveLab\Component\ModelTransformer\Transformer\TransformableModelTransformer;
use Silex\Application;
use Silex\ServiceProviderInterface;

class ModelTransformerServiceProvider implements ServiceProviderInterface
{
    /**
     * {@inheritDoc}
     */
    public function register(Application $app)
    {
        if (!$app instanceof AppKernel) {
            throw UnexpectedTypeException::create($app, AppKernel::class);
        }

        $app['model_transformer'] = $app->share(function (AppKernel $kernel) {
            $modelTransformer = new ModelTransformerManager();

            $modelTransformer->addTransformer(new TraversableModelTransformer());
            $modelTransformer->addTransformer(new TransformableModelTransformer());
            $modelTransformer->addTransformer(new DefaultORMPaginationModelTransformer());
            $modelTransformer->addTransformer(new UserModelTransformer());
            $modelTransformer->addTransformer(new ClientModelTransformer());
            $modelTransformer->addTransformer(new DiaryModelTransformer());
            $modelTransformer->addTransformer(new DiaryMoneyModelTransformer());

            $metadataFactory = new MetadataFactory($kernel->getAnnotationReader());
            $cachedMetadataFactory = new CachedMetadataFactory($metadataFactory, $kernel->getCache());
            $annotatedModelTransformer = new AnnotatedModelTransformer($cachedMetadataFactory);

            $modelTransformer->addTransformer($annotatedModelTransformer);

            return $modelTransformer;
        });
    }

    /**
     * {@inheritDoc}
     */
    public function boot(Application $app)
    {
    }
}
