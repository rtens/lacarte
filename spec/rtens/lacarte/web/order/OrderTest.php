<?php
namespace spec\rtens\lacarte\web\order;

use rtens\lacarte\OrderInteractor;
use rtens\lacarte\core\NotFoundException;
use rtens\lacarte\model\Dish;
use rtens\lacarte\model\Menu;
use rtens\lacarte\model\Order;
use rtens\lacarte\model\Selection;
use rtens\lacarte\model\User;
use rtens\mockster\Mock;
use spec\rtens\lacarte\Test;
use spec\rtens\lacarte\web\ComponentTest;
use spec\rtens\lacarte\web\ComponentTest_Given;
use watoki\collections\Liste;
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

    /** @var Dish[] */
    public $dishes;

    /** @var array|Menu[] */
    public $menus = array();

    /** @var array|Set[] */
    public $selections = array();

    /** @var Menu[] */
    public $menusByDate = array();

    public $selectionsByDishId = array();

    function __construct(Test $test) {
        parent::__construct($test);
        $this->orderInteractor = $this->test->mf->createMock(OrderInteractor::$CLASS);
        $this->dishes = new Map();
        $this->orderInteractor->__mock()->method('readSelectionByMenuIdAndUserId')->willThrow(new NotFoundException());

        $that = $this;
        $this->orderInteractor->__mock()->method('readAllMenusByDate')->willCall(function (\DateTime $date) use ($that) {
            return isset($that->menusByDate[$date->format('Ymd')]) ? $that->menusByDate[$date->format('Ymd')] : new Set();
        });
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
            $date = new \DateTime('2000-01-' . ($m + 3));
            $menu = new Menu($this->order->id, $date);
            $this->menusByDate[$date->format('Ymd')] = new Set(array($menu));

            $menu->id = $m + 1;
            $this->menus[$menu->id] = $menu;
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
        $this->orderInteractor->__mock()->method('readMenuById')->willCall(function ($id) use ($that) {
            return $that->menus[$id];
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

    public function theMenu_HasASelectionForNoDish($menuId) {
        $this->theMenu_HasASelectionForDish($menuId, 0);
    }

    public function theMenu_HasASelectionForDish($menuId, $dishId) {
        $this->selections[$menuId] = new Selection($this->me->id, $menuId, $dishId);
        $this->selections[$menuId]->id = count($this->selections);
        $this->orderInteractor->__mock()->method('readSelectionByMenuIdAndUserId')
            ->willReturn($this->selections[$menuId])->withArguments($menuId, $this->me->id);
    }

    public function anErrorOccurs($string) {
        $this->orderInteractor->__mock()->method('readById')->willThrow(new \Exception($string));
    }

    public function _wasDeleted($name) {
        $this->userInteractor->__mock()->method('readById')->willThrow(new NotFoundException())
            ->withArguments($this->users[$name]->id);
    }

    public function _SelectedDish_ForMenu($user, $dishId, $menuId) {
        $selection = new Selection($this->users[$user]->id, $menuId, $dishId);
        $selection->id = $this->users[$user]->id + 100;
        $this->selectionsByDishId[$dishId][] = $selection;

        $this->orderInteractor->__mock()->method('readSelectionByMenuIdAndUserId')
            ->willReturn($selection)
            ->withArguments($menuId, $this->users[$user]->id);
        $this->orderInteractor->__mock()->method('readAllSelectionsByDishId')
            ->willReturn(new Set($this->selectionsByDishId[$dishId]))
            ->withArguments($dishId);
    }

}