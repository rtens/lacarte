<?php
namespace spec\rtens\lacarte\web\order;
 
use rtens\lacarte\OrderInteractor;
use rtens\lacarte\model\Order;
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
        $this->given->theOrder_WithDeadline('Test Order 1', '2013-04-03 18:00');
        $this->given->theOrder_WithDeadline('Test Order 2', '2013-04-02 18:00');
        $this->given->theOrder_WithDeadline('Test Order 3', '2013-04-01 18:00');

        $this->when->iAccessThePage();

        $this->then->_shouldHaveTheSize('order', 3);
        $this->then->_shouldBe('order/0/name', 'Test Order 1');
        $this->then->_shouldBe('order/0/deadline', '03.04.2013 18:00');
        $this->then->_shouldBe('order/0/url/href', 'selection.html?order=1');
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
 * @property ListComponent component
 * @property ListComponent component
 */
class ListTest_When extends ComponentTest_When {

    function __construct(Test $test) {
        parent::__construct($test);

        $this->component = $test->mf->createTestUnit(ListComponent::$CLASS, array(
            'factory' => $this->test->factory,
            'route' => new Path(),
            'session' => $this->test->given->session,
            'orderInteractor' => $this->test->given->orderInteractor
        ));
        $this->component->__mock()->method('subComponent')->setMocked();
    }

    public function iAccessThePage() {
        $this->model = $this->component->doGet();
    }
}