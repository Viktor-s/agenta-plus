<?php

namespace AgentPlus\ServiceProvider;

use AgentPlus\AppKernel;
use AgentPlus\Component\Validator\Cache;
use AgentPlus\Component\Validator\Validator;
use FiveLab\Component\Exception\UnexpectedTypeException;
use FiveLab\Component\VarTagValidator\Metadata\CachedMetadataFactory;
use FiveLab\Component\VarTagValidator\Metadata\MetadataFactory;
use FiveLab\Component\VarTagValidator\VarTagValidator;
use Silex\Application;
use Silex\ServiceProviderInterface;
use Symfony\Component\Validator\ValidatorBuilder;

class ValidatorServiceProvider implements ServiceProviderInterface
{
    /**
     * {@inheritDoc}
     */
    public function register(Application $app)
    {
        if (!$app instanceof AppKernel) {
            throw UnexpectedTypeException::create($app, AppKernel::class);
        }

        $app['validator'] = $app->share(function (AppKernel $kernel) {
            $builder = new ValidatorBuilder();
            $builder
                ->enableAnnotationMapping($kernel->getAnnotationReader())
                ->setMetadataCache(new Cache($kernel->getCache()));

            $sfValidator = $builder->getValidator();

            $varTagMetadataFactory = new MetadataFactory();
            $varTagCachedMetadataFactory = new CachedMetadataFactory($varTagMetadataFactory, $kernel->getCache());
            $varTagValidator = new VarTagValidator($sfValidator, $varTagCachedMetadataFactory);

            return new Validator($sfValidator, $varTagValidator);
        });
    }

    /**
     * {@inheritDoc}
     */
    public function boot(Application $app)
    {
    }
}
