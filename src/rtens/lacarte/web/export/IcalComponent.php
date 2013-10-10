<?php


namespace rtens\lacarte\web\export;


use rtens\lacarte\core\Session;
use rtens\lacarte\OrderInteractor;
use rtens\lacarte\UserInteractor;
use rtens\lacarte\utils\CsvRenderer;
use rtens\lacarte\web\DefaultComponent;
use watoki\curir\controller\Module;
use watoki\curir\Path;
use watoki\factory\Factory;

class IcalComponent extends DefaultComponent {

    static $CLASS = __CLASS__;

    private $orderInteractor;

    function __construct(Factory $factory, Path $route, Module $parent = null,
                         UserInteractor $userInteractor, Session $session, OrderInteractor $orderInteractor) {
        parent::__construct($factory, $route, $parent, $userInteractor, $session);
        $this->orderInteractor = $orderInteractor;
        $this->session = $session;
    }

    /**
     * @param int $order ID of order to display
     * @return array
     */
    public function doGet() {
        $this->orderInteractor->
        var_dump($this->session->get('key'));
        die();
        return $this->redirect(Url::parse('selections.html?order=' . $order));
    }

}