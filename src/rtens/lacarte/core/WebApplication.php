<?php
namespace rtens\lacarte\core;

use rtens\lacarte\web\LaCarte;
use watoki\collections\Liste;
use watoki\curir\Path;
use watoki\curir\Request;
use watoki\curir\controller\Module;
use watoki\factory\Factory;

class WebApplication {

    private $route;

    private $factory;

    function __construct($route) {
        $this->route = new Path(new Liste(array($route)));
        $this->factory = new Factory();
    }

    public function handleRequest($request, $moduleClass) {
        /** @var $module Module */
        $module = $this->factory->getInstance($moduleClass, array(
            'route' => $this->route
        ));
        $module->respond(Request::build($request))->flush();
    }
}