<?php

use cfg\rtens\lacarte\DefaultConfiguration;
use cfg\rtens\lacarte\UserConfiguration;
use rtens\lacarte\core\Configuration;

require __DIR__ . '/vendor/autoload.php';

$factory = new \watoki\factory\Factory();
$factory->setProvider('StdClass', new \watoki\factory\providers\PropertyInjectionProvider($factory));

require_once __DIR__ . '/config/DefaultConfiguration.php';

$userConfig = __DIR__ . '/config/UserConfiguration.php';
if (file_exists($userConfig)) {
    require_once $userConfig;
    $factory->setSingleton(Configuration::Configuration, new UserConfiguration());
} else {
    $factory->setSingleton(Configuration::Configuration, new DefaultConfiguration());
}


return $factory;