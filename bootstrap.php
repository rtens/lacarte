<?php

use rtens\lacarte\core\Configuration;

require __DIR__ . '/vendor/autoload.php';

$factory = new \watoki\factory\Factory();

$configFile = __DIR__ . '/usr/UserConfiguration.php';

$configLoader = new \watoki\cfg\Loader($factory);
$configLoader->loadConfiguration(Configuration::$CLASS, $configFile, array('rootDir' => __DIR__));

return $factory;