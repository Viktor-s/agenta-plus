<?php

namespace AgentPlus\Behat\KernelAwareExtension;

use AgentPlus\AppKernel;
use AgentPlus\Component\Kernel\KernelAwareInterface;
use Behat\Behat\Context\Context;
use Behat\Behat\Context\Initializer\ContextInitializer;
use Behat\Behat\EventDispatcher\Event\ExampleTested;
use Behat\Behat\EventDispatcher\Event\ScenarioTested;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class KernelAwareInitializer implements ContextInitializer, EventSubscriberInterface
{
    /**
     * @var AppKernel
     */
    private $kernel;

    /**
     * Construct
     *
     * @param AppKernel $kernel
     */
    public function __construct(AppKernel $kernel)
    {
        $this->kernel = $kernel;
    }

    /**
     * {@inheritDoc}
     */
    public function initializeContext(Context $context)
    {
        if ($context instanceof KernelAwareInterface) {
            $context->setKernel($this->kernel);
        }
    }

    /**
     * Reboot kernel
     */
    public function rebootKernel()
    {
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            ScenarioTested::AFTER  => array('rebootKernel', -15),
            ExampleTested::AFTER   => array('rebootKernel', -15),
        );
    }
}
