<?php
namespace rtens\lacarte\web\common;
 
use rtens\lacarte\core\Session;
use watoki\curir\Path;
use watoki\curir\controller\Component;
use watoki\curir\controller\Module;
use watoki\factory\Factory;

class MenuComponent extends Component {

    public static $CLASS = __CLASS__;

    private $session;

    function __construct(Factory $factory, Path $route, Module $parent = null,
            Session $session) {
        parent::__construct($factory, $route, $parent);

        $this->session = $session;
    }

    public function doGet() {
        return array(
            'isAdmin' => $this->session->has('admin')
        );
    }

}
