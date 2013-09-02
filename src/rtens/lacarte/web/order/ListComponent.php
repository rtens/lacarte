<?php
namespace rtens\lacarte\web\order;

use rtens\lacarte\OrderInteractor;
use rtens\lacarte\UserInteractor;
use rtens\lacarte\core\Session;
use rtens\lacarte\utils\TimeService;
use rtens\lacarte\web\DefaultComponent;
use watoki\collections\Set;
use watoki\curir\Path;
use watoki\curir\Url;
use watoki\curir\controller\Module;
use watoki\factory\Factory;

class ListComponent extends DefaultComponent {

    public static $CLASS = __CLASS__;

    private $time;

    private $orderInteractor;

    function __construct(
        Factory $factory,
        Path $route,
        Module $parent = null,
        UserInteractor $userInteractor,
        Session $session,
        OrderInteractor $orderInteractor,
        TimeService $time
    ) {

        parent::__construct($factory, $route, $parent, $userInteractor, $session);
        $this->orderInteractor = $orderInteractor;
        $this->time = $time;
    }

    public function doGet() {
        return $this->assembleModel();
    }

    protected function assembleModel($model = array()) {
        return parent::assembleModel(
            array_merge(
                array(
                    'order' => $this->assembleOrders(),
                    'firstDay' => array('value' => $this->time->fromString('monday next week')->format('Y-m-d')),
                    'lastDay' => array('value' => $this->time->fromString('friday next week')->format('Y-m-d')),
                    'deadline' => array(
                        'value' => $this->time->fromString('thursday this week 18:00')->format(
                            'Y-m-d H:i'
                        )
                    ),
                    'error' => null,
                    'today' => $this->getTodaysOrder()
                ),
                $model
            )
        );
    }

    public function doPost($firstDay, $lastDay, $deadline) {
        if (!$this->isAdmin()) {
            return $this->assembleModel(
                array(
                    'error' => 'Access denied.'
                )
            );
        }
        try {
            $order = $this->orderInteractor->createOrder(
                $this->getAdminGroupId(),
                new \DateTime($firstDay),
                new \DateTime($lastDay),
                new \DateTime($deadline)
            );
            return $this->redirect(Url::parse('edit.html?order=' . $order->id));
        } catch (\Exception $e) {
            return $this->assembleModel(
                array(
                    'error' => $e->getMessage()
                )
            );
        }
    }

    private function assembleOrders() {
        $orders = array();
        foreach ($this->orderInteractor->readAll() as $order) {
            $orders[] = array(
                'name' => $order->getName(),
                'deadline' => $order->getDeadline()->format('d.m.Y H:i'),
                'isOpen' => $order->getDeadline() > $this->time->now(),
                'itemLink' => $this->makeLink($this->isAdmin() ? 'selections' : 'select', $order),
                'editLink' => $this->makeLink('edit', $order),
                'selectLink' => $this->makeLink('select', $order),
            );
        }
        return $orders;
    }

    private function makeLink($component, $order) {
        return array(
            'href' => $component . '.html?order=' . $order->id
        );
    }

    private function getTodaysOrder() {
        if (!$this->isUser()) {
            return;
        }
        $menus = $this->orderInteractor->readAllMenusByDate($this->time->fromString('today'));
        if (count($menus) < 1) {
            return;
        }
        foreach ($menus as $menu) {
            if (!$menu->id) {}
            $selections = $this->orderInteractor->readSelectionByMenuIdAndUserId(
                $menu->id,
                $this->getLoggedInUser()->id
            );
            if (count($this->orderInteractor->readDishById($selections->getDishId())) < 1) {
                return array('dish' => 'Nothing for you today');
            }
            $dish = $this->orderInteractor->readDishById($selections->getDishId())->getText();
        }

        return array('dish' => $dish);
    }

}
