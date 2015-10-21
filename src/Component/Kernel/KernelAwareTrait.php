<?php

namespace AgentPlus\Component\Kernel;

use AgentPlus\AppKernel;

trait KernelAwareTrait
{
    /**
     * @var AppKernel
     */
    protected $kernel;

    /**
     * Set kernel
     *
     * @param AppKernel $kernel
     *
     * @return self
     */
    public function setKernel(AppKernel $kernel)
    {
        $this->kernel = $kernel;

        return $this;
    }
}
