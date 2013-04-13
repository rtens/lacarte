<?php

use cfg\rtens\lacarte\UserConfiguration;
use rtens\lacarte\core\Configuration;

require __DIR__ . '/vendor/autoload.php';

require_once __DIR__ . '/config/DefaultConfiguration.php';
require_once __DIR__ . '/config/UserConfiguration.php';

$factory = new \watoki\factory\Factory();
$factory->setSingleton(Configuration::Configuration, new UserConfiguration());

return $factory;