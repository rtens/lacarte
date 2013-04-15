<?php

$factory = require_once 'bootstrap.php';

$app = new \rtens\lacarte\core\WebApplication($_REQUEST['_'], $factory);
$app->handleRequest($_REQUEST['-'], \rtens\lacarte\web\LaCarteModule::$CLASS);