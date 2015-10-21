<?php

umask(0000);

use AgentPlus\AppKernel;

include_once __DIR__ . '/../app/autoload.php';
include_once __DIR__ . '/../app/AppKernel.php';

$kernel = new AppKernel('dev', true);
$kernel->boot();
$kernel->run();
