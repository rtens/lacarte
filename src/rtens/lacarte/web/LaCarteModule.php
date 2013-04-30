<?php
namespace rtens\lacarte\web;

use rtens\lacarte\web\user\LoginComponent;
use watoki\collections\Liste;
use watoki\curir\Path;
use watoki\curir\Request;
use watoki\curir\controller\Module;
use watoki\curir\renderer\RendererFactory;
use watoki\curir\router\RedirectRouter;
use watoki\curir\router\StaticRouter;
use watoki\factory\Factory;
use watoki\tempan\Renderer;

class LaCarteModule extends Module {

    static $CLASS = __CLASS__;

    protected function createRouters() {
        return new Liste(array(
            new RedirectRouter(new Path(new Liste(array(''))), 'user/login.html'),
        ));
    }

    public function respond(Request $request) {
        try {
            return parent::respond($request);
        } catch (\Exception $e) {
            $this->getResponse()->setBody('Sorry, something unexpected happened :( <!--' . "\n\n" .$e);
            return $this->getResponse();
        }
    }

}