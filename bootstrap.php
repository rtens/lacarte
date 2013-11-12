<?php

use rtens\lacarte\core\Configuration;

require __DIR__ . '/vendor/autoload.php';

$factory = new \watoki\factory\Factory();

$configLoader = new \watoki\cfg\Loader($factory);
$configLoader->loadConfiguration(Configuration::$CLASS, __DIR__ . '/config/UserConfiguration.php', array('rootDir' => __DIR__));

return $factory;