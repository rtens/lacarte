<?php

$factory = require_once 'bootstrap.php';


try {
    $app = new \watoki\curir\WebApplication(\rtens\lacarte\WebResource::$CLASS, $factory);
    $app->run();
} catch (Exception $e) {
    echo "Something went wrong. Sorry. <!-- " . $e;
}