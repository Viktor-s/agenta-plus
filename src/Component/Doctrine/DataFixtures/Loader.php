<?php

namespace AgentPlus\Component\Doctrine\DataFixtures;

use AgentPlus\AppKernel;
use AgentPlus\Component\Kernel\KernelAwareInterface;
use Doctrine\Common\DataFixtures\Loader as BaseLoader;

class Loader extends BaseLoader
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
    public function getFixtures()
    {
        $fixtures = parent::getFixtures();

        foreach ($fixtures as $fixture) {
            if ($fixture instanceof KernelAwareInterface) {
                $fixture->setKernel($this->kernel);
            }
        }

        return $fixtures;
    }
}
