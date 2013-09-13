<?php
namespace spec\rtens\lacarte\specs\order;

use rtens\mockster\MockFactory;
use spec\rtens\lacarte\fixtures\component\order\ListComponentFixture;
use spec\rtens\lacarte\fixtures\model\OrderFixture;
use spec\rtens\lacarte\fixtures\service\SessionFixture;
use spec\rtens\lacarte\fixtures\service\TimeFixture;
use spec\rtens\lacarte\Specification;

/**
 * @property SessionFixture session <-
 * @property TimeFixture time <-
 * @property OrderFixture order <-
 * @property ListComponentFixture component <-
 */
class CreateOrderTest extends Specification {

    function testAutoFillFields() {
        $this->session->givenIAmLoggedInAsAdmin();
        $this->time->givenNowIs('2013-04-01');

        $this->component->whenIOpenThePage();

        $this->component->thenTheFirstDayFieldShouldContain('2013-04-08');
        $this->component->thenTheLastDayFieldShouldContain('2013-04-12');
        $this->component->thenTheDeadlineFieldShouldContain('2013-04-04 18:00');
        $this->component->thenThereShouldBeNoErrorMessage();
    }

    function testOneWeek() {
        $this->session->givenIAmLoggedInAsAdmin();
        $this->component->givenIHaveEnteredTheFirstDay('2013-04-08');
        $this->component->givenIHaveEnteredTheLastDay('2013-04-12');
        $this->component->givenIHaveEnteredTheDeadline('2013-04-04 18:00');

        $this->component->whenICreateANewOrder();

        $this->component->thenIShouldBeRedirectedTo('edit.html?order=1');

        $this->order->thenThereShouldBe_Orders(1);
        $this->order->thenThereShouldBeAnOrderWithTheName('08.04.2013 - 12.04.2013');
        $this->order->thenThisOrderShouldHaveTheDeadline('2013-04-04 18:00');
        $this->order->thenThisOrderShouldHave_Menus(5);
        $this->order->thenTheDateOfMenu_OfThisOrderShouldBe(1, '2013-04-08');
        $this->order->thenTheDateOfMenu_OfThisOrderShouldBe(5, '2013-04-12');
        $this->order->thenMenu_OfThisOrderShouldHave_Dishes(1, 3);
        $this->order->thenMenu_OfThisOrderShouldHave_Dishes(2, 3);
        $this->order->thenMenu_OfThisOrderShouldHave_Dishes(5, 3);
    }

    function testOverTheWeekend() {
        $this->session->givenIAmLoggedInAsAdmin();
        $this->component->givenIHaveEnteredTheFirstDay('2013-04-03');
        $this->component->givenIHaveEnteredTheLastDay('2013-04-13');
        $this->component->givenIHaveEnteredTheDeadline('2013-04-01 18:00');

        $this->component->whenICreateANewOrder();

        $this->order->thenThereShouldBe_Orders(1);
        $this->order->thenThereShouldBeAnOrderWithTheName('03.04.2013 - 13.04.2013');
        $this->order->thenThisOrderShouldHave_Menus(8);
        $this->order->thenTheDateOfMenu_OfThisOrderShouldBe(1, '2013-04-03');
        $this->order->thenTheDateOfMenu_OfThisOrderShouldBe(2, '2013-04-04');
        $this->order->thenTheDateOfMenu_OfThisOrderShouldBe(3, '2013-04-05');
        $this->order->thenTheDateOfMenu_OfThisOrderShouldBe(4, '2013-04-08');
        $this->order->thenTheDateOfMenu_OfThisOrderShouldBe(5, '2013-04-09');
        $this->order->thenTheDateOfMenu_OfThisOrderShouldBe(6, '2013-04-10');
        $this->order->thenTheDateOfMenu_OfThisOrderShouldBe(7, '2013-04-11');
        $this->order->thenTheDateOfMenu_OfThisOrderShouldBe(8, '2013-04-12');
    }

    function testNotAdmin() {
        $this->component->givenIHaveEnteredTheFirstDay('2013-04-03');
        $this->component->givenIHaveEnteredTheLastDay('2013-04-13');
        $this->component->givenIHaveEnteredTheDeadline('2013-04-01 18:00');

        $this->component->whenICreateANewOrder();

        $this->component->thenTheErrorMessageShouldBe('Access denied.');
    }

    function testWrongFormat() {
        $this->session->givenIAmLoggedInAsAdmin();
        $this->component->givenIHaveEnteredTheFirstDay('not a date');

        $this->component->whenICreateANewOrder();

        $this->component->thenTheErrorMessageShouldContain('Failed to parse time string');
    }

    function testEndBeforeStart() {
        $this->session->givenIAmLoggedInAsAdmin();
        $this->component->givenIHaveEnteredTheFirstDay('2013-04-13');
        $this->component->givenIHaveEnteredTheLastDay('2013-04-03');
        $this->component->givenIHaveEnteredTheDeadline('2013-04-01 18:00');

        $this->component->whenICreateANewOrder();

        $this->component->thenTheErrorMessageShouldBe('First day must be before last day');
    }

    function testDeadlineAfterStart() {
        $this->session->givenIAmLoggedInAsAdmin();
        $this->component->givenIHaveEnteredTheFirstDay('2013-04-03');
        $this->component->givenIHaveEnteredTheLastDay('2013-04-13');
        $this->component->givenIHaveEnteredTheDeadline('2013-04-04 18:00');

        $this->component->whenICreateANewOrder();

        $this->component->thenTheErrorMessageShouldBe('Deadline must be before or on first day');
    }

}