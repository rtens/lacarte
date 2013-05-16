<?php
namespace spec\rtens\lacarte\web\order;

use rtens\lacarte\web\order\SelectionComponent;
use spec\rtens\lacarte\Test;
use spec\rtens\lacarte\web\ComponentTest_When;

/**
 * @property SelectionTest_When when
 */
class SelectionTest extends OrderTest {

    function testLoadOrder() {
        $this->given->iAmLoggedInAsUser();
        $this->given->anOrder_With_MenusEach_Dishes('Test Order', 3, 2);
        $this->given->dish_OfMenu_Is(1, 1, 'Dish One A');
        $this->given->dish_OfMenu_Is(2, 1, 'Dish One B');
        $this->given->dish_OfMenu_Is(1, 2, 'Dish Two A');
        $this->given->dish_OfMenu_Is(2, 2, 'Dish Two B');
        $this->given->dish_OfMenu_Is(1, 3, 'Dish Three A');
        $this->given->dish_OfMenu_Is(2, 3, 'Dish Three B');
        $this->given->theMenu_HasASelectionForDish(1, 1);
        $this->given->theMenu_HasASelectionForDish(2, 4);
        $this->given->theMenu_HasASelectionForDish(3, 0);

        $this->when->iAccessThePage();

        $this->then->_shouldBe('error', null);
        $this->then->_shouldBe('order/name', 'Test Order');
        $this->then->_shouldHaveTheSize('order/selection', 3);
        $this->then->_shouldBe('order/selection/0/date', 'Monday, 3.1.2000');
        $this->then->_shouldBe('order/selection/0/dish', 'Dish One A');
        $this->then->_shouldBe('order/selection/1/date', 'Tuesday, 4.1.2000');
        $this->then->_shouldBe('order/selection/1/dish', 'Dish Two B');
        $this->then->_shouldBe('order/selection/2/date', 'Wednesday, 5.1.2000');
        $this->then->_shouldBe('order/selection/2/dish', 'You selected no dish');

        $this->then->_shouldHaveTheSize('order/selection/0/notSelected', 1);
        $this->then->_shouldBe('order/selection/0/notSelected/0', 'Dish One B');
        $this->then->_shouldHaveTheSize('order/selection/1/notSelected', 1);
        $this->then->_shouldBe('order/selection/1/notSelected/0', 'Dish Two A');
        $this->then->_shouldHaveTheSize('order/selection/2/notSelected', 2);
        $this->then->_shouldBe('order/selection/2/notSelected/0', 'Dish Three A');
        $this->then->_shouldBe('order/selection/2/notSelected/1', 'Dish Three B');
    }

    function testAsAdmin() {
        $this->given->anOrder_With_MenusEach_Dishes('Test Order', 3, 2);
        $this->given->iAmLoggedInAsAdmin();

        $this->when->iAccessThePage();

        $this->then->iShouldBeRedirectedTo('selections.html?order=12');
    }

    function testError() {
        $this->given->anOrder_With_MenusEach_Dishes('Test Order', 3, 2);
        $this->given->iAmLoggedInAsUser();
        $this->given->anErrorOccurs('Something terrible happened');

        $this->when->iAccessThePage();

        $this->then->_shouldBe('error', 'Something terrible happened');
        $this->then->_shouldBe('order', null);
    }

}

/**
 * @property SelectionTest test
 * @property SelectionComponent component
 */
class SelectionTest_When extends ComponentTest_When {

    function __construct(Test $test) {
        parent::__construct($test);
        $this->createDefaultComponent(SelectionComponent::$CLASS, array(
            'orderInteractor' => $this->test->given->orderInteractor
        ));
    }

    public function iAccessThePage() {
        $this->model = $this->component->doGet($this->test->given->order->id);
    }
}