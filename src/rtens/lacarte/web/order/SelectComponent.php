<?php
namespace rtens\lacarte\web\order;

use rtens\lacarte\OrderInteractor;
use rtens\lacarte\UserInteractor;
use rtens\lacarte\core\Session;
use rtens\lacarte\model\Menu;
use rtens\lacarte\model\Order;
use rtens\lacarte\utils\TimeService;
use rtens\lacarte\web\DefaultComponent;
use watoki\curir\Path;
use watoki\curir\controller\Module;
use watoki\factory\Factory;

class SelectComponent extends DefaultComponent {

    public static $CLASS = __CLASS__;

    private $time;

    private $orderInteractor;

    function __construct(Factory $factory, Path $route, Module $parent = null,
                         UserInteractor $userInteractor, Session $session, OrderInteractor $orderInteractor,
                         TimeService $time) {
        parent::__construct($factory, $route, $parent, $userInteractor, $session);
        $this->orderInteractor = $orderInteractor;
        $this->time = $time;
    }

    public function doGet($order) {
        return $this->assembleMyModel($order);
    }

    private function assembleMyModel($orderId, $model = array()) {
        return $this->assembleModel(array_merge(array(
            'error' => null,
            'success' => null,
            'order' => $this->assembleOrder($this->orderInteractor->readById($orderId))
        ), $model));
    }

    private function assembleOrder(Order $order) {
        return array(
            'timeLeft' => $this->time->until($order->getDeadline())->format('%dd %hh %im'),
            'menu' => $this->assembleMenus($order)
        );
    }

    private function assembleMenus(Order $order) {
        $menus = array();
        foreach ($this->orderInteractor->readMenusByOrderId($order->id) as $menu) {
            $menus[] = array(
                'date' => $menu->getDate()->format('l, j.n.Y'),
                'key' => $this->assembleKey($menu),
                'dish' => $this->assembleDishes($menu)
            );
        }
        return $menus;
    }

    private function assembleKey(Menu $menu, $value = 0) {
        return array(
            'name' => "selection[{$menu->id}]",
            'value' => $value
        );
    }

    private function assembleDishes(Menu $menu) {
        $dishes = array();
        foreach ($this->orderInteractor->readDishesByMenuId($menu->id) as $dish) {
            $dishes[] = array(
                'key' => $this->assembleKey($menu, $dish->id),
                'text' => $dish->getText()
            );
        }
        return $dishes;
    }

}