<?php
namespace spec\rtens\lacarte\web\order;

use rtens\lacarte\web\order\SelectComponent;
use spec\rtens\lacarte\Test;
use spec\rtens\lacarte\web\ComponentTest_When;

/**
 * @property SelectTest_Given given
 * @property SelectTest_When when
 */
class SelectTest extends OrderTest {

    function testNoSelection() {
        $this->given->anOrder_With_MenusEach_Dishes('test', 2, 2);
        $this->given->theDeadlineOfTheOrderIs('2000-01-01 00:00');
        $this->given->nowIs('1999-12-29 21:38');
        $this->given->dish_OfMenu_Is(1, 1, 'A');
        $this->given->dish_OfMenu_Is(2, 1, 'B');
        $this->given->dish_OfMenu_Is(1, 2, 'C');
        $this->given->dish_OfMenu_Is(2, 2, 'D');

        $this->when->iAccessThePageForTheOrder('test');

        $this->then->_shouldBe('error', null);
        $this->then->_shouldBe('success', null);
        $this->then->_shouldBe('timeLeft', '2d 2h 22m');
        $this->then->_shouldHaveTheSize('menu', 2);
        $this->then->_shouldBe('menu/0/date', 'Monday, 3.1.2000');
        $this->then->_shouldBe('menu/0/key/name', 'selection[1]');
        $this->then->_shouldHaveTheSize('menu/0/dish', 2);
        $this->then->_shouldBe('menu/0/dish/0/key/name', 'selection[1]');
        $this->then->_shouldBe('menu/0/dish/0/key/value', '1');
        $this->then->_shouldBe('menu/0/dish/0/text', 'A');
    }

    function testSaveSelection() {
        $this->markTestIncomplete();
    }

    function testLoadSelection() {
        $this->markTestIncomplete();
    }

}

/**
 * @property SelectTest test
 */
class SelectTest_Given extends OrderTest_Given {

}

/**
 * @property SelectTest test
 * @property SelectComponent component
 */
class SelectTest_When extends ComponentTest_When {

    function __construct(Test $test) {
        parent::__construct($test);
        $this->createDefaultComponent(SelectComponent::$CLASS, array(
            'orderInteractor' => $this->test->given->orderInteractor,
            'time' => $this->test->given->time
        ));
    }

    public function iAccessThePageForTheOrder() {
        $this->model = $this->component->doGet($this->test->given->order->id);
    }
}