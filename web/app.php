<?php

use AgentPlus\AppKernel;

include_once __DIR__ . '/../app/autoload.php';
include_once __DIR__ . '/../app/AppKernel.php';

$kernel = new AppKernel('prod', false);
$kernel->boot();
$kernel->run();
