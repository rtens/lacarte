<?php
namespace spec\rtens\lacarte\web\order;
 
use rtens\lacarte\OrderInteractor;
use rtens\lacarte\model\Dish;
use rtens\lacarte\model\Menu;
use rtens\lacarte\model\Order;
use rtens\lacarte\web\order\EditComponent;
use rtens\mockster\Mock;
use spec\rtens\lacarte\Test;
use spec\rtens\lacarte\web\ComponentTest;
use spec\rtens\lacarte\web\ComponentTest_Given;
use spec\rtens\lacarte\web\ComponentTest_Then;
use spec\rtens\lacarte\web\ComponentTest_When;
use watoki\collections\Collection;
use watoki\collections\Map;
use watoki\collections\Set;
use watoki\curir\Path;

/**
 * @property EditOrderTest_Given given
 * @property EditOrderTest_When when
 * @property EditOrderTest_Then then
 */
class EditOrderTest extends ComponentTest {

    function testLoadDishes() {
        $this->given->anOrder_With_MenusEach_Dishes('test', 2, 2);
        $this->given->dish_OfMenu_Is(1, 1, 'A');
        $this->given->dish_OfMenu_Is(2, 1, 'B');
        $this->given->dish_OfMenu_Is(1, 2, 'C');
        $this->given->dish_OfMenu_Is(2, 2, 'D');

        $this->when->iAccessThePage();

        $this->then->_shouldBe('error', null);
        $this->then->_shouldBe('success', null);

        $this->then->_shouldBe('order/name', 'test');
        $this->then->_shouldBe('order/id/value', 12);

        $this->then->_shouldHaveTheSize('order/menu', 2);
        $this->then->_shouldBe('order/menu/0/date', 'Monday, 3.1.2000');
        $this->then->_shouldBe('order/menu/1/date', 'Tuesday, 4.1.2000');
        $this->then->_shouldHaveTheSize('order/menu/0/dish', 2);
        $this->then->_shouldBe('order/menu/0/dish/0/text/_', 'A');
        $this->then->_shouldBe('order/menu/0/dish/1/text/_', 'B');
        $this->then->_shouldBe('order/menu/1/dish/0/text/_', 'C');
        $this->then->_shouldBe('order/menu/1/dish/1/text/_', 'D');

        $this->then->_shouldBe('order/menu/0/dish/0/text/name', 'dish[1]');
        $this->then->_shouldBe('order/menu/0/dish/1/text/name', 'dish[2]');
        $this->then->_shouldBe('order/menu/1/dish/0/text/name', 'dish[3]');
        $this->then->_shouldBe('order/menu/1/dish/1/text/name', 'dish[4]');
    }

    function testSaveDishes() {
        $this->given->anOrder_With_MenusEach_Dishes('test2', 2, 3);
        $this->given->iHaveEntered_ForDish_OfMenu('W', 1, 1);
        $this->given->iHaveEntered_ForDish_OfMenu('X', 2, 1);
        $this->given->iHaveEntered_ForDish_OfMenu('Y', 1, 2);
        $this->given->iHaveEntered_ForDish_OfMenu('Z', 3, 2);

        $this->when->iSaveTheDishes();

        $this->then->_DishesShouldBeSaved(6);

        $this->then->_shouldBe('success', 'Order saved');
        $this->then->_shouldBe('error', null);
        $this->then->_shouldBe('order/name', 'test2');
    }

    function testError() {
        $this->given->anOrder_With_MenusEach_Dishes('error', 1, 1);
        $this->given->anErrorOccurs('Something went wrong');

        $this->when->iSaveTheDishes();

        $this->then->_shouldBe('error', 'Something went wrong');
    }

}

class EditOrderTest_Given extends ComponentTest_Given {

    /** @var Order|null */
    public $order;

    public $dishesOfMenus;

    /** @var Mock */
    public $orderInteractor;

    public $enteredDishes;

    function __construct(Test $test) {
        parent::__construct($test);
        $this->orderInteractor = $this->test->mf->createMock(OrderInteractor::$CLASS);
        $this->enteredDishes = new Map();
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
                $dish->id = $this->enteredDishes->count() + 1;
                $this->enteredDishes->set($dish->id, $dish);
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
            return $that->enteredDishes[$id];
        });
    }

    public function dish_OfMenu_Is($dishNum, $menuNum, $text) {
        /** @var Dish $dish */
        $dish = $this->dishesOfMenus[$menuNum][$dishNum - 1];
        $dish->setText($text);
    }

    public function iHaveEntered_ForDish_OfMenu($text, $dishNum, $menuNum) {
        $this->dish_OfMenu_Is($dishNum, $menuNum, $text);
    }

    public function anErrorOccurs($string) {
        $this->orderInteractor->__mock()->method('updateDishes')->willThrow(new \Exception($string));
    }
}

/**
 * @property EditOrderTest test
 * @property EditComponent component
 */
class EditOrderTest_When extends ComponentTest_When {

    function __construct(Test $test) {
        parent::__construct($test);
        $this->component = $test->mf->createTestUnit(EditComponent::$CLASS, array(
            'factory' => $this->test->factory,
            'path' => new Path(),
            'orderInteractor' => $this->test->given->orderInteractor
        ));
        $this->component->__mock()->method('subComponent')->setMocked();
    }

    public function iAccessThePage() {
        $this->model = $this->component->doGet($this->test->given->order->id);
    }

    public function iSaveTheDishes() {
        $this->model = $this->component->doPost($this->test->given->order->id, $this->test->given->enteredDishes);
    }
}

/**
 * @property EditOrderTest test
 */
class EditOrderTest_Then extends ComponentTest_Then {

    public function _DishesShouldBeSaved($count) {
        $method = $this->test->given->orderInteractor->__mock()->method('updateDishes');
        $this->test->assertTrue($method->wasCalled(), 'updateDishes was not called');

        /** @var Collection $dishes */
        $dishes = $method->getCalledArgumentAt(0, 0);
        $this->test->assertEquals($count, $dishes->count());
    }
}
