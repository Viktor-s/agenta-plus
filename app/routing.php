<?php

use AgentPlus\Controller\Api\ControllerProvider as ApiControllerProvider;
use AgentPlus\Controller\Cabinet\ControllerProvider as MainControllerProvider;

$this->mount('/api', new ApiControllerProvider());
$this->mount('/', new MainControllerProvider());