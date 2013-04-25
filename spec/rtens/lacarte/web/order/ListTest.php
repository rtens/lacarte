<?php
namespace spec\rtens\lacarte\web\order;
 
use rtens\lacarte\OrderInteractor;
use rtens\lacarte\model\Order;
use rtens\lacarte\utils\TimeService;
use rtens\lacarte\web\order\ListComponent;
use spec\rtens\lacarte\Test;
use spec\rtens\lacarte\web\ComponentTest;
use spec\rtens\lacarte\web\ComponentTest_Given;
use spec\rtens\lacarte\web\ComponentTest_When;
use watoki\collections\Liste;
use watoki\curir\Path;

/**
 * @property ListTest_Given given
 * @property ListTest_When when
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
        $this->then->_shouldBe('order/0/url/href', 'select.html?order=1');
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

        $this->then->_shouldBe('order/0/url/href', 'edit.html?order=1');
    }

}

class ListTest_given extends ComponentTest_Given {

    function __construct(Test $test) {
        parent::__construct($test);
        $this->orders = new Liste();

        $this->orderInteractor = $test->mf->createMock(OrderInteractor::$CLASS);
        $this->orderInteractor->__mock()->method('readAll')->willReturn($this->orders);
    }

    public function theOrder_WithDeadline($name, $date) {
        $order = new Order($this->group->id, $name, new \DateTime($date));
        $order->id = 1;
        $this->orders->append($order);
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