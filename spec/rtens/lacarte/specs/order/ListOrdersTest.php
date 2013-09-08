<?php
namespace spec\rtens\lacarte\specs\order;

use rtens\mockster\MockFactory;
use spec\rtens\lacarte\fixtures\component\order\ListComponentFixture;
use spec\rtens\lacarte\fixtures\model\OrderFixture;
use spec\rtens\lacarte\fixtures\service\SessionFixture;
use spec\rtens\lacarte\fixtures\service\TimeFixture;
use spec\rtens\lacarte\TestCase;

class ListOrdersTest extends TestCase {

    /** @var ListComponentFixture */
    public $component;

    /** @var \spec\rtens\lacarte\fixtures\service\TimeFixture */
    public $time;

    /** @var OrderFixture */
    public $order;

    /** @var \spec\rtens\lacarte\fixtures\service\SessionFixture */
    public $session;

    public function testEmptyList() {
        $this->component->whenIOpenThePage();
        $this->component->thenThereShouldBe_OrdersListed(0);
    }

    function testFourOrders() {
        $this->time->givenNowIs('2013-04-02 19:00');
        $this->order->givenTheOrder_WithDeadline('Test Order 1', '2013-04-04 18:00');
        $this->order->givenTheOrder_WithDeadline('Test Order 2', '2013-04-03 18:00');
        $this->order->givenTheOrder_WithDeadline('Test Order 3', '2013-04-02 18:00');
        $this->order->givenTheOrder_WithDeadline('Test Order 4', '2013-04-01 18:00');

        $this->component->whenIOpenThePage();

        $this->component->thenThereShouldBe_OrdersListed(4);
        $this->component->thenTheNameOfOrder_ShouldBe(1, 'Test Order 1');
        $this->component->thenTheDeadlineOfOrder_ShouldBe(1, '04.04.2013 18:00');
        $this->component->thenTheSelectLinkOfOrder_ShouldBe(1, 'select.html?order=1');
        $this->component->thenTheEditLinkOfOrder_ShouldBe(1, 'edit.html?order=1');
        $this->component->thenTheItemLinkOfOrder_ShouldBe(1, 'select.html?order=1');
        $this->component->thenOrder_ShouldBeOpen(1);

        $this->component->thenTheNameOfOrder_ShouldBe(2, 'Test Order 2');
        $this->component->thenTheDeadlineOfOrder_ShouldBe(2, '03.04.2013 18:00');
        $this->component->thenOrder_ShouldBeOpen(2);

        $this->component->thenTheNameOfOrder_ShouldBe(3, 'Test Order 3');
        $this->component->thenTheDeadlineOfOrder_ShouldBe(3, '02.04.2013 18:00');
        $this->component->thenOrder_ShouldNotBeOpen(3);
    }

    function testShowSelectionsLinkToAdmin() {
        $this->time->givenNowIs('2013-04-02 19:00');
        $this->session->givenIAmLoggedInAsAdmin();
        $this->order->givenTheOrder_WithDeadline('Test Order 1', '2013-04-03 18:00');

        $this->component->whenIOpenThePage();

        $this->component->thenTheItemLinkOfOrder_ShouldBe(1, 'selections.html?order=1');
    }

    protected function setUp() {
        parent::setUp();

        $this->session = $this->useFixture(SessionFixture::$CLASS);
        $this->time = $this->useFixture(TimeFixture::$CLASS);
        $this->component = $this->useFixture(ListComponentFixture::$CLASS);
        $this->order = $this->useFixture(OrderFixture::$CLASS);
    }
}