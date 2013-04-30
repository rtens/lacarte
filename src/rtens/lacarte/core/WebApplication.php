<?php
namespace rtens\lacarte\core;

use rtens\lacarte\web\LaCarteModule;
use watoki\collections\Liste;
use watoki\curir\Path;
use watoki\curir\Request;
use watoki\curir\controller\Module;
use watoki\factory\Factory;

class WebApplication {

    private $route;

    private $factory;

    function __construct($route, Factory $factory = null) {
        $this->factory = $factory ?: new Factory();
        $nodes = $route ? new Liste(array($route)) : new Liste();
        $this->route = new Path($nodes);
    }

    public function handleRequest($request, $moduleClass) {
        /** @var $module Module */
        $module = $this->factory->getInstance($moduleClass, array(
            'route' => $this->route
        ));
        $module->respond(Request::build($request))->flush();
    }
}