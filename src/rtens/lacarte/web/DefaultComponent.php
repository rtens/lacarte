<?php
namespace rtens\lacarte\web;

use rtens\lacarte\core\Session;
use watoki\curir\Path;
use watoki\curir\composition\SuperComponent;
use watoki\curir\controller\Module;
use watoki\factory\Factory;

abstract class DefaultComponent extends SuperComponent {

    /**
     * @var \rtens\lacarte\core\Session
     */
    protected $session;

    function __construct(Factory $factory, Path $route, Module $parent = null, Session $session) {
        parent::__construct($factory, $route, $parent);

        $this->session = $session;
    }

}