<?php

require_once 'bootstrap.php';

$app = new \rtens\lacarte\core\WebApplication($_REQUEST['_']);
$app->handleRequest($_REQUEST['-'], \rtens\lacarte\web\LaCarte::$CLASS);