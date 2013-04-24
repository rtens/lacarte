<?php
namespace spec\rtens\lacarte\features\order;
 
use rtens\lacarte\OrderInteractor;
use rtens\lacarte\model\Order;
use rtens\lacarte\model\stores\OrderStore;
use spec\rtens\lacarte\Test;
use spec\rtens\lacarte\Test_Given;
use spec\rtens\lacarte\Test_Then;
use spec\rtens\lacarte\Test_When;

/**
 * @property ReadAllTest_Given given
 * @property ReadAllTest_When when
 * @property ReadAllTest_Then then
 */
class ReadAllTest extends Test {

    function testListAll() {
        $this->given->theOrder_WithDeadline('test1', '2013-04-01');
        $this->given->theOrder_WithDeadline('test3', '2013-04-03');
        $this->given->theOrder_WithDeadline('test2', '2013-04-02');

        $this->when->iReadAllOrders();

        $this->then->order_ShouldBe(1, 'test3');
        $this->then->order_ShouldBe(2, 'test2');
        $this->then->order_ShouldBe(3, 'test1');
    }

}

class ReadAllTest_Given extends Test_Given {

    function __construct(Test $test, OrderStore $store) {
        parent::__construct($test);
        $this->store = $store;
    }

    public function theOrder_WithDeadline($name, $date) {
        $this->store->create(new Order(42, $name, new \DateTime($date)));
    }
}

class ReadAllTest_When extends Test_When {

    /** @var Order[] */
    public $orders;

    function __construct(Test $test, OrderInteractor $interactor) {
        parent::__construct($test);
        $this->interactor = $interactor;
    }

    public function iReadAllOrders() {
        $this->orders = $this->interactor->readAll();
    }
}

/**
 * @property ReadAllTest test
 * @property ReadAllTest test
 */
class ReadAllTest_Then extends Test_Then {

    public function order_ShouldBe($index, $name) {
        $this->test->assertEquals($name, $this->test->when->orders[$index - 1]->getName());
    }
}
