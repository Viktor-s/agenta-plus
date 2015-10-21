<?php

namespace AgentPlus\Behat\KernelAwareExtension;

use AgentPlus\AppKernel;
use Behat\Behat\Context\ServiceContainer\ContextExtension;
use Behat\Testwork\ServiceContainer\Extension;
use Behat\Testwork\ServiceContainer\ExtensionManager;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class KernelAwareExtension implements Extension
{
    /**
     * {@inheritDoc}
     */
    public function process(ContainerBuilder $container)
    {

    }

    /**
     * {@inheritDoc}
     */
    public function getConfigKey()
    {
        return 'ap_kernel_aware';
    }

    /**
     * {@inheritDoc}
     */
    public function initialize(ExtensionManager $extensionManager)
    {
    }

    /**
     * {@inheritDoc}
     */
    public function configure(ArrayNodeDefinition $builder)
    {
    }

    /**
     * {@inheritDoc}
     */
    public function load(ContainerBuilder $container, array $config)
    {
        $this->loadKernel($container);
        $this->loadInitializer($container);
    }

    /**
     * Load kernel
     *
     * @param ContainerBuilder $container
     */
    private function loadKernel(ContainerBuilder $container)
    {
        $appDir = realpath(__DIR__ . '/../../../app');
        $autoloadFile = $appDir . '/autoload.php';
        $kernelFile = $appDir . '/AppKernel.php';
        require_once $autoloadFile;
        require_once $kernelFile;

        $kernel = new AppKernel('test', true);

        $container->set('ap_kernel_aware.kernel', $kernel);
    }

    /**
     * Load initializer
     *
     * @param ContainerBuilder $container
     */
    public function loadInitializer(ContainerBuilder $container)
    {
        $initializerDefinition = new Definition(KernelAwareInitializer::class);
        $initializerDefinition->addTag(ContextExtension::INITIALIZER_TAG);
        $initializerDefinition->setArguments([new Reference('ap_kernel_aware.kernel')]);

        $container->setDefinition('ap_kernel_aware.initializer', $initializerDefinition);
    }
}
