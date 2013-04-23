<?php
namespace rtens\lacarte\web\order;
 
use rtens\lacarte\OrderInteractor;
use rtens\lacarte\UserInteractor;
use rtens\lacarte\core\Session;
use rtens\lacarte\utils\TimeService;
use rtens\lacarte\web\DefaultComponent;
use watoki\curir\Path;
use watoki\curir\Url;
use watoki\curir\controller\Module;
use watoki\factory\Factory;

class ListComponent extends DefaultComponent {

    public static $CLASS = __CLASS__;

    private $time;

    private $orderInteractor;

    function __construct(Factory $factory, Path $route, Module $parent = null,
            UserInteractor $userInteractor, Session $session, OrderInteractor $orderInteractor, TimeService $time) {

        parent::__construct($factory, $route, $parent, $userInteractor, $session);
        $this->orderInteractor = $orderInteractor;
        $this->time = $time;
    }

    public function doGet() {
        return $this->assembleModel();
    }

    protected function assembleModel($model = array()) {
        return parent::assembleModel(array_merge(array(
            'firstDay' => $this->time->fromString('monday next week')->format('Y-m-d'),
            'lastDay' => $this->time->fromString('friday next week')->format('Y-m-d'),
            'deadline' => $this->time->fromString('thursday this week 18:00')->format('Y-m-d H:i'),
            'error' => null
        ), $model));
    }

    public function doPost($firstDay, $lastDay, $deadline) {
        try {
            $order = $this->orderInteractor->createOrder(new \DateTime($firstDay), new \DateTime($lastDay), new \DateTime($deadline));
            return $this->redirect(Url::parse('edit.html?order=' . $order->id));
        } catch (\Exception $e) {
            return $this->assembleModel(array(
                'error' => $e->getMessage()
            ));
        }
    }

}
