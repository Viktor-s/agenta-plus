<?php

namespace AgentPlus\Component\Kernel;

use AgentPlus\AppKernel;

interface KernelAwareInterface
{
    /**
     * Set app kernel
     *
     * @param AppKernel $kernel
     */
    public function setKernel(AppKernel $kernel);
}
