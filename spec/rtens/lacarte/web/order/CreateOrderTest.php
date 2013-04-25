<?php
namespace spec\rtens\lacarte\web\order;
 
use rtens\lacarte\OrderInteractor;
use rtens\lacarte\UserInteractor;
use rtens\lacarte\model\Order;
use rtens\lacarte\utils\TimeService;
use rtens\lacarte\web\order\ListComponent;
use spec\rtens\lacarte\Test;
use spec\rtens\lacarte\web\ComponentTest;
use spec\rtens\lacarte\web\ComponentTest_Given;
use spec\rtens\lacarte\web\ComponentTest_Then;
use spec\rtens\lacarte\web\ComponentTest_When;
use watoki\curir\Path;

/**
 * @property CreateOrderTest_Given given
 * @property CreateOrderTest_When when
 * @property CreateOrderTest_Then then
 */
class CreateOrderTest extends ComponentTest {

    function testFillInputFields() {
        $this->given->nowIs('2013-04-01');
        $this->when->iAccessPage();

        $this->then->_shouldBe('firstDay/value', '2013-04-08');
        $this->then->_shouldBe('lastDay/value', '2013-04-12');
        $this->then->_shouldBe('deadline/value', '2013-04-04 18:00');
        $this->then->_shouldBe('error', null);
    }

    function testSuccess() {
        $this->given->iAmLoggedInAsAdmin();
        $this->given->iHaveEnteredTheFirstDay('2013-04-02');
        $this->given->iHaveEnteredTheLastDay('2013-04-10');
        $this->given->iHaveEnteredTheDeadline('2013-04-01 16:00');
        $this->given->theIdOfTheCreatedOrderIs(42);

        $this->when->iCreateANewOrder();

        $this->then->anOrderShouldBeCreatedForGroup_Between_And_WithDeadline(42, '2013-04-02', '2013-04-10', '2013-04-01 16:00');
        $this->then->iShouldBeRedirectedTo('edit.html?order=42');
    }

    function testNotAdmin() {
        $this->when->iCreateANewOrder();

        $this->then->_shouldContain('error', 'denied');
    }

    function testWrongFormat() {
        $this->given->iAmLoggedInAsAdmin();
        $this->given->iHaveEnteredTheFirstDay('not a date');

        $this->when->iCreateANewOrder();

        $this->then->_shouldContain('error', 'parse time string');
    }

    function testError() {
        $this->given->iAmLoggedInAsAdmin();
        $this->given->somethingGoesWrong('Some error');

        $this->when->iCreateANewOrder();

        $this->then->_shouldBe('error','Some error');
    }

}

/**
 * @property CreateOrderTest test
 */
class CreateOrderTest_Given extends ComponentTest_Given {

    public $firstDay;

    public $lastDay;

    public $deadline;

    function __construct(Test $test) {
        parent::__construct($test);
        $this->userInteractor = $test->mf->createMock(UserInteractor::$CLASS);
        $this->orderInteractor = $test->mf->createMock(OrderInteractor::$CLASS);
    }

    public function iHaveEnteredTheFirstDay($date) {
        $this->firstDay = $date;
    }

    public function iHaveEnteredTheLastDay($date) {
        $this->lastDay = $date;
    }

    public function iHaveEnteredTheDeadline($date) {
        $this->deadline = $date;
    }

    public function theIdOfTheCreatedOrderIs($id) {
        $order = new Order(1, 'bla', new \DateTime());
        $order->id = $id;
        $this->orderInteractor->__mock()->method('createOrder')->willReturn($order);
    }

    public function somethingGoesWrong($string) {
        $this->orderInteractor->__mock()->method('createOrder')->willThrow(new \Exception($string));
    }
}

/**
 * @property CreateOrderTest test
 * @property ListComponent component
 */
class CreateOrderTest_When extends ComponentTest_When {

    function __construct(Test $test) {
        parent::__construct($test);
        $this->createDefaultComponent(ListComponent::$CLASS, array(
            'userInteractor' => $this->test->given->userInteractor,
            'orderInteractor' => $this->test->given->orderInteractor,
            'time' => $this->test->given->time
        ));
    }

    public function iAccessPage() {
        $this->model = $this->component->doGet();
    }

    public function iCreateANewOrder() {
        $this->model = $this->component->doPost($this->test->given->firstDay,
            $this->test->given->lastDay, $this->test->given->deadline);
    }
}

/**
 * @property CreateOrderTest test
 */
class CreateOrderTest_Then extends ComponentTest_Then {

    public function anOrderShouldBeCreatedForGroup_Between_And_WithDeadline($groupId, $first, $last, $deadline) {
        $this->test->assertTrue($this->test->given->orderInteractor->__mock()->method('createOrder')
                ->wasCalledWith(array($groupId, new \DateTime($first), new \DateTime($last), new \DateTime($deadline))));
    }
}