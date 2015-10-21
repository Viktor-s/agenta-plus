<?php

use AgentPlus\Controller\Api\ControllerProvider as ApiControllerProvider;
use AgentPlus\Controller\Cabinet\ControllerProvider as MainControllerProvider;

/** @var \AgentPlus\AppKernel $this */

$host = $this['host'];
$apiHost = $this['host_api'];
$cpHost = $this->getCabineHost();

$this->mount('/', new ApiControllerProvider(), $apiHost);
$this->mount('/', new MainControllerProvider, $cpHost);