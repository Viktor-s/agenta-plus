<?php

namespace AgentPlus\ServiceProvider;

use AgentPlus\AppKernel;
use AgentPlus\Model\Normalizer\DiaryModelNormalizer;
use AgentPlus\Model\Normalizer\OrderModelNormalizer;
use FiveLab\Component\Exception\UnexpectedTypeException;
use FiveLab\Component\ModelNormalizer\ModelNormalizerManager;
use FiveLab\Component\ModelNormalizer\Normalizer\Annotated\AnnotatedModelNormalizer;
use FiveLab\Component\ModelNormalizer\Normalizer\Annotated\Metadata\CachedMetadataFactory;
use FiveLab\Component\ModelNormalizer\Normalizer\Annotated\Metadata\MetadataFactory;
use FiveLab\Component\ModelNormalizer\Normalizer\DateTimeModelNormalizer;
use FiveLab\Component\ModelNormalizer\Normalizer\NormalizableModelNormalizer;
use FiveLab\Component\ModelNormalizer\Normalizer\TraversableModelNormalizer;
use Silex\Application;
use Silex\ServiceProviderInterface;

class ModelNormalizerServiceProvider implements ServiceProviderInterface
{
    /**
     * {@inheritDoc}
     */
    public function register(Application $app)
    {
        if (!$app instanceof AppKernel) {
            throw UnexpectedTypeException::create($app, AppKernel::class);
        }

        $app['model_normalizer'] = $app->share(function (AppKernel $kernel) {
            $modelNormalizer = new ModelNormalizerManager();

            $modelNormalizer->addNormalizer(new TraversableModelNormalizer());
            $modelNormalizer->addNormalizer(new NormalizableModelNormalizer());
            $modelNormalizer->addNormalizer(new DateTimeModelNormalizer());

            $modelNormalizer->addNormalizer(new OrderModelNormalizer());
            $modelNormalizer->addNormalizer(new DiaryModelNormalizer());

            $metadataFactory = new MetadataFactory($kernel->getAnnotationReader());
            $cachedMetadataFactory = new CachedMetadataFactory($metadataFactory, $kernel->getCache());
            $annotatedModelNormalizer = new AnnotatedModelNormalizer($cachedMetadataFactory);

            $modelNormalizer->addNormalizer($annotatedModelNormalizer);

            return $modelNormalizer;
        });
    }

    /**
     * {@inheritDoc}
     */
    public function boot(Application $app)
    {
    }
}
