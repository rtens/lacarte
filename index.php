<?php
use rtens\lacarte\WebResource;
use watoki\curir\http\Url;
use watoki\curir\WebApplication;

/** @var \watoki\factory\Factory $factory */
$factory = require_once 'bootstrap.php';

try {
    /** @var \rtens\lacarte\core\Configuration $config */
    $config = $factory->getInstance(\rtens\lacarte\core\Configuration::$CLASS);

    $app = new WebApplication($factory->getInstance(WebResource::$CLASS, array(Url::parse($config->getHost()))));
    $app->run();
} catch (Exception $e) {
    echo "Something went wrong. Sorry. <!-- " . $e;
}