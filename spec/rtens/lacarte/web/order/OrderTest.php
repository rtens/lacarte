<?php
namespace spec\rtens\lacarte\web\order;

use rtens\lacarte\OrderInteractor;
use rtens\lacarte\model\Dish;
use rtens\lacarte\model\Menu;
use rtens\lacarte\model\Order;
use rtens\mockster\Mock;
use spec\rtens\lacarte\Test;
use spec\rtens\lacarte\web\ComponentTest;
use spec\rtens\lacarte\web\ComponentTest_Given;
use watoki\collections\Map;
use watoki\collections\Set;

/**
 * @property OrderTest_Given given
 */
abstract class OrderTest extends ComponentTest {

}

/**
 * @property OrderTest test
 */
class OrderTest_Given extends ComponentTest_Given {

    /** @var Order|null */
    public $order;

    public $dishesOfMenus;

    /** @var Mock */
    public $orderInteractor;

    /** @var  */
    public $dishes;

    function __construct(Test $test) {
        parent::__construct($test);
        $this->orderInteractor = $this->test->mf->createMock(OrderInteractor::$CLASS);
        $this->dishes = new Map();
    }

    public function anOrder_With_MenusEach_Dishes($name, $numMenus, $numDishes) {
        $this->order = new Order($this->group->id, $name, new \DateTime('2000-01-01'));
        $this->order->id = 12;

        $menus = array(
            $this->order->id => new Set()
        );
        $dishes = array();
        $this->dishesOfMenus = array();

        for ($m = 0; $m < $numMenus; $m++) {
            $menu = new Menu($this->order->id, new \DateTime('2000-01-' . ($m + 3)));
            $menu->id = $m + 1;
            $menus[$this->order->id][] = $menu;
            $dishes[$menu->id] = new Set();

            for ($d = 0; $d < $numDishes; $d++) {
                $dish = new Dish($menu->id, '');
                $dish->id = $this->dishes->count() + 1;
                $this->dishes->set($dish->id, $dish);
                $dishes[$menu->id][] = $dish;

                $this->dishesOfMenus[$menu->id][] = $dish;
            }
        }

        $this->orderInteractor->__mock()->method('readById')->willReturn($this->order);
        $this->orderInteractor->__mock()->method('readMenusByOrderId')->willCall(function ($id) use ($menus) {
            return $menus[$id];
        });
        $this->orderInteractor->__mock()->method('readDishesByMenuId')->willCall(function ($id) use ($dishes) {
            return $dishes[$id];
        });
        $that = $this;
        $this->orderInteractor->__mock()->method('readDishById')->willCall(function ($id) use ($that) {
            return $that->dishes[$id];
        });
    }

    public function dish_OfMenu_Is($dishNum, $menuNum, $text) {
        /** @var Dish $dish */
        $dish = $this->dishesOfMenus[$menuNum][$dishNum - 1];
        $dish->setText($text);
    }

    public function theDeadlineOfTheOrderIs($date) {
        $this->order->setDeadline(new \DateTime($date));
    }

}