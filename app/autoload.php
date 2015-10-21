<?php

use Doctrine\Common\Annotations\AnnotationRegistry;

/** @var \Composer\Autoload\ClassLoader $loader */
$loader = require __DIR__ . '/../vendor/autoload.php';

if (is_object($loader)) {
    AnnotationRegistry::registerLoader([$loader, 'loadClass']);
}

date_default_timezone_set('UTC');
