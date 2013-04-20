<?php

$factory = require_once 'bootstrap.php';

$route = $_REQUEST['_'];
$request = $_REQUEST['-'];
unset($_REQUEST['_']);
unset($_REQUEST['-']);

$app = new \rtens\lacarte\core\WebApplication($route, $factory);
$app->handleRequest($request, \rtens\lacarte\web\LaCarteModule::$CLASS);