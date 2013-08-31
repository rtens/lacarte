<?php
namespace spec\rtens\lacarte\web\order;

use rtens\lacarte\OrderInteractor;
use rtens\lacarte\core\NotFoundException;
use rtens\lacarte\model\Dish;
use rtens\lacarte\model\Menu;
use rtens\lacarte\model\Order;
use rtens\lacarte\model\Selection;
use rtens\lacarte\utils\TimeService;
use rtens\lacarte\web\order\ListComponent;
use spec\rtens\lacarte\Test;
use spec\rtens\lacarte\web\ComponentTest;
use spec\rtens\lacarte\web\ComponentTest_Given;
use spec\rtens\lacarte\web\ComponentTest_Then;
use spec\rtens\lacarte\web\ComponentTest_When;
use watoki\collections\Liste;
use watoki\collections\Map;
use watoki\curir\Path;

/**
 * @property ListTest_Given given
 * @property ListTest_When when
 * @property ListTest_Then then
 */
class ListTest extends ComponentTest {

    function testEmptyList() {
        $this->when->iAccessThePage();
        $this->then->_shouldHaveTheSize('order', 0);
    }

    function testListAll() {
        $this->given->nowIs('2013-04-02 19:00');
        $this->given->theOrder_WithDeadline('Test Order 1', '2013-04-04 18:00');
        $this->given->theOrder_WithDeadline('Test Order 2', '2013-04-03 18:00');
        $this->given->theOrder_WithDeadline('Test Order 3', '2013-04-02 18:00');
        $this->given->theOrder_WithDeadline('Test Order 4', '2013-04-01 18:00');

        $this->when->iAccessThePage();

        $this->then->_shouldHaveTheSize('order', 4);
        $this->then->_shouldBe('order/0/name', 'Test Order 1');
        $this->then->_shouldBe('order/0/deadline', '04.04.2013 18:00');
        $this->then->_shouldBe('order/0/selectLink/href', 'select.html?order=1');
        $this->then->_shouldBe('order/0/editLink/href', 'edit.html?order=1');
        $this->then->_shouldBe('order/0/itemLink/href', 'select.html?order=1');
        $this->then->_shouldBe('order/0/isOpen', true);

        $this->then->_shouldBe('order/1/name', 'Test Order 2');
        $this->then->_shouldBe('order/1/deadline', '03.04.2013 18:00');
        $this->then->_shouldBe('order/1/isOpen', true);

        $this->then->_shouldBe('order/2/name', 'Test Order 3');
        $this->then->_shouldBe('order/2/deadline', '02.04.2013 18:00');
        $this->then->_shouldBe('order/2/isOpen', false);
    }

    function testWhenAdmin() {
        $this->given->iAmLoggedInAsAdmin();
        $this->given->theOrder_WithDeadline('Test Order 1', '2013-04-03 18:00');

        $this->when->iAccessThePage();

        $this->then->_shouldBe('order/0/itemLink/href', 'selections.html?order=1');
    }

    function testShowTodaysSelection() {
        $this->given->iAmLoggedInAsUser();
        $this->given->nowIs('2013-04-04 18:00');
        $this->given->theOrder_WithDeadline('order1', '2013-04-03 18:00');
        $this->given->theMenu_OfTheOrder_ForTheDay('Menu 1', 'order1', '2013-04-04');
        $this->given->theMenu_HasADish('Menu 1', 'Dish 1');
        $this->given->iHaveSelectedDish_ForMenu('Dish 1', 'Menu 1');

        $this->when->iAccessThePage();

        $this->then->itShouldDisplayTodaysOrder('Dish 1');
    }

    function testNoTodaysSelectionIfNotUser() {
        $this->given->iAmLoggedInAsAdmin();
        $this->given->nowIs('2013-04-04 18:00');
        $this->given->theOrder_WithDeadline('order1', '2013-04-03 18:00');
        $this->given->theMenu_OfTheOrder_ForTheDay('Menu 1', 'order1', '2013-04-04');
        $this->given->theMenu_HasADish('Menu 1', 'Dish 1');

        $this->when->iAccessThePage();

        $this->then->noTodaysOrderShouldBeDisplayed();
    }

    function testNoTodaysSelectionIfNothingSelected() {
        $this->given->iAmLoggedInAsUser();
        $this->given->nowIs('2013-04-04 18:00');
        $this->given->theOrder_WithDeadline('order1', '2013-04-03 18:00');
        $this->given->theMenu_OfTheOrder_ForTheDay('Menu 1', 'order1', '2013-04-04');
        $this->given->theMenu_HasADish('Menu 1', 'Dish 1');

        $this->when->iAccessThePage();

        $this->then->noTodaysOrderShouldBeDisplayed();
    }

    function testTodaysSelectionWhenSelectedNoDish() {
        $this->given->iAmLoggedInAsUser();
        $this->given->nowIs('2013-04-04 18:00');
        $this->given->theOrder_WithDeadline('order1', '2013-04-03 18:00');
        $this->given->theMenu_OfTheOrder_ForTheDay('Menu 1', 'order1', '2013-04-04');
        $this->given->theMenu_HasADish('Menu 1', 'Dish 1');
        $this->given->iHaveSelectedNoDishForMenu('Menu 1');

        $this->when->iAccessThePage();

        $this->then->itShouldDisplayTodaysOrder('Nothing for you today');
    }

}

class ListTest_given extends ComponentTest_Given {

    function __construct(Test $test) {
        parent::__construct($test);
        $this->orders = new Liste();
        $this->menus = new Map();
        $this->dishes = new Map();

        $this->orderInteractor = $test->mf->createMock(OrderInteractor::$CLASS);
        $this->orderInteractor->__mock()->method('readAll')->willReturn($this->orders);
        $this->orderInteractor->__mock()->method('readAllMenusByDate')->willReturn($this->menus->values());
        $this->orderInteractor->__mock()->method('readSelectionByMenuIdAndUserId')->willThrow(new NotFoundException());
    }

    public function theOrder_WithDeadline($name, $date) {
        $order = new Order($this->group->id, $name, new \DateTime($date));
        $order->id = 1;
        $this->orders->append($order);
    }

    public function theMenu_OfTheOrder_ForTheDay($menuName, $orderName, $date) {
        $menu = new Menu($this->orders->first(), new \DateTime($date));
        $this->menus->set($menuName, $menu);
        $this->orderInteractor->__mock()->method('readAllMenusByDate')->willReturn($this->menus->values());
    }

    public function theMenu_HasADish($menu, $dishName) {
        $dish = new Dish($this->menus[$menu]->id, $dishName);
        $this->dishes->set($dishName, $dish);
        $dish->id = $this->dishes->count() + 1;
        $this->orderInteractor->__mock()->method('readDishById')->willReturn($dish)->withArguments($dish->id);
    }

    public function iHaveSelectedDish_ForMenu($dish, $menu) {
        $selection = new Selection($this->me->id, $this->menus[$menu]->id, $this->dishes[$dish]->id);
        $this->orderInteractor->__mock()->method('readSelectionByMenuIdAndUserId')->willReturn($selection);
    }

    public function iHaveSelectedNoDishForMenu($menu) {
        $selection = new Selection($this->me->id, $this->menus[$menu]->id, null);
        $this->orderInteractor->__mock()->method('readSelectionByMenuIdAndUserId')->willReturn($selection);
    }
}

/**
 * @property ListTest test
 * @property ListComponent component
 */
class ListTest_When extends ComponentTest_When {

    function __construct(Test $test) {
        parent::__construct($test);
        $this->createDefaultComponent(ListComponent::$CLASS, array(
            'orderInteractor' => $this->test->given->orderInteractor,
            'time' => $this->test->given->time
        ));
    }

    public function iAccessThePage() {
        $this->model = $this->component->doGet();
    }
}

/**
 * @property ListTest test
 */
class ListTest_Then extends ComponentTest_Then {

    public function itShouldDisplayTodaysOrder($string) {
        $this->test->assertEquals($string, $this->getField('today/dish'));
    }

    public function noTodaysOrderShouldBeDisplayed() {
        $this->test->assertNull($this->getField('today'));
    }
}