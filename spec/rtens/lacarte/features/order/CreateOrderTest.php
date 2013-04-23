<?php
namespace spec\rtens\lacarte\features\order;

use rtens\lacarte\OrderInteractor;
use rtens\lacarte\model\Order;
use spec\rtens\lacarte\Test;
use spec\rtens\lacarte\Test_Then;
use spec\rtens\lacarte\Test_When;

/**
 * @property CreateOrderTest_When when
 * @property CreateOrderTest_Then then
 */
class CreateOrderTest extends Test {

    function testOneWeek() {
        $this->when->iCreateAnOrderBetween_And_WithDeadline('2013-04-08', '2013-04-12', '2013-04-04 18:00');

        $this->then->thereShouldBeAnOrder();
        $this->then->theNameShouldBe('08.04.2013 - 12.04.2013');
        $this->then->theDeadlineShouldBe('2013-04-04 18:00');
        $this->then->theOrderShouldHave_Menus(5);
        $this->then->theDateOfMenu_ShouldBe(0, '2013-04-08');
        $this->then->theDateOfMenu_ShouldBe(4, '2013-04-12');
        $this->then->eachMenuShouldHave_Dishes(3);
    }

    function testOverTheWeekend() {
        $this->when->iCreateAnOrderBetween_And_WithDeadline('2013-04-03', '2013-04-13', '2013-04-01 18:00');

        $this->then->thereShouldBeAnOrder();
        $this->then->theOrderShouldHave_Menus(8);
        $this->then->theDateOfMenu_ShouldBe(0, '2013-04-03');
        $this->then->theDateOfMenu_ShouldBe(7, '2013-04-12');
    }

    function testEndBeforeStart() {
        $this->when->iTryToCreateAnOrderBetween_And_WithDeadline('2013-04-13', '2013-04-03', '2013-04-01 18:00');
        $this->then->anExceptionShouldBeThrownContaining('before');
    }

    function testDeadlineAfterStart() {
        $this->when->iTryToCreateAnOrderBetween_And_WithDeadline('2013-04-03', '2013-04-13', '2013-04-04 18:00');
        $this->then->anExceptionShouldBeThrownContaining('before');
    }

}

/**
 * @property CreateOrderTest test
 */
class CreateOrderTest_When extends Test_When {

    /** @var Order */
    public $order;

    /** @var OrderInteractor */
    public $orderInteractor;

    function __construct(Test $test) {
        parent::__construct($test);

        $this->orderInteractor = $this->test->factory->getInstance(OrderInteractor::$CLASS);
    }

    public function iCreateAnOrderBetween_And_WithDeadline($start, $end, $deadline) {
        $groupId = 1;
        $this->order = $this->orderInteractor->createOrder(
            $groupId, new \DateTime($start), new \DateTime($end), new \DateTime($deadline));
    }

    public function iTryToCreateAnOrderBetween_And_WithDeadline($start, $end, $deadline) {
        try {
            $this->iCreateAnOrderBetween_And_WithDeadline($start, $end, $deadline);
        } catch (\Exception $e) {
            $this->caught = $e;
        }
    }
}

/**
 * @property CreateOrderTest test
 */
class CreateOrderTest_Then extends Test_Then {

    public function thereShouldBeAnOrder() {
        $this->test->assertFalse($this->test->when->orderInteractor->readAll()->isEmpty());
    }

    public function theOrderShouldHave_Menus($int) {
        $this->test->assertEquals($int, $this->test->when->orderInteractor->readMenusByOrderId(
            $this->test->when->order->id)->count());
    }

    public function eachMenuShouldHave_Dishes($int) {
        foreach ($this->getMenus() as $menu) {
            $this->test->assertEquals($int, $this->test->when->orderInteractor->readDishesByMenuId($menu->id)->count());
        }
    }

    public function theNameShouldBe($string) {
        $this->test->assertEquals($string, $this->test->when->order->getName());
    }

    public function theDeadlineShouldBe($date) {
        $this->test->assertEquals(new \DateTime($date), $this->test->when->order->getDeadline());
    }

    public function theDateOfMenu_ShouldBe($int, $date) {
        $this->test->assertEquals(new \DateTime($date), $this->getMenus()->get($int)->getDate());
    }

    /**
     * @return \rtens\lacarte\model\Menu[]|\watoki\collections\Liste
     */
    private function getMenus() {
        return $this->test->when->orderInteractor->readMenusByOrderId($this->test->when->order->id);
    }
}