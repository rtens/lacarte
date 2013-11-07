<?php

/** @var \watoki\factory\Factory $factory */
$factory = require_once 'bootstrap.php';

try {
    /** @var \rtens\lacarte\core\Configuration $config */
    $config = $factory->getInstance(\rtens\lacarte\core\Configuration::Configuration);
    $app = new \watoki\curir\WebApplication(\rtens\lacarte\WebResource::$CLASS,
        \watoki\curir\http\Url::parse($config->getHost()), $factory);
    $app->run();
} catch (Exception $e) {
    echo "Something went wrong. Sorry. <!-- " . $e;
}