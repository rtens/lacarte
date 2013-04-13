<?php
namespace rtens\lacarte\web;

use rtens\lacarte\web\user\Login;
use watoki\collections\Liste;
use watoki\curir\Path;
use watoki\curir\controller\Module;
use watoki\curir\router\RedirectRouter;
use watoki\curir\router\StaticRouter;

class LaCarte extends Module {

    static $CLASS = __CLASS__;

    protected function createRouters() {
        return new Liste(array(
            new RedirectRouter(new Path(new Liste(array(''))), 'user/login.html')
        ));
    }

}