<?php
namespace rtens\lacarte\web;

use rtens\lacarte\web\user\Login;
use watoki\collections\Liste;
use watoki\curir\Path;
use watoki\curir\controller\Module;
use watoki\curir\renderer\RendererFactory;
use watoki\curir\router\RedirectRouter;
use watoki\curir\router\StaticRouter;
use watoki\factory\Factory;
use watoki\tempan\Renderer;

class LaCarte extends Module {

    static $CLASS = __CLASS__;

    protected function createRouters() {
        return new Liste(array(
            new RedirectRouter(new Path(new Liste(array(''))), 'user/login.html')
        ));
    }

    function __construct(Factory $factory, Path $route, Module $parent = null) {
        parent::__construct($factory, $route, $parent);

        /** @var $rendererFactory RendererFactory */
        $rendererFactory = $factory->getInstance(RendererFactory::$CLASS);
        $rendererFactory->setRenderer('html', Renderer::$CLASS);
        $factory->setSingleton(RendererFactory::$CLASS, $rendererFactory);
    }

}