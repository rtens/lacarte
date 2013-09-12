<?php
namespace spec\rtens\lacarte\specs\order;

use spec\rtens\lacarte\fixtures\component\order\ListComponentFixture;
use spec\rtens\lacarte\fixtures\model\OrderFixture;
use spec\rtens\lacarte\fixtures\service\SessionFixture;
use spec\rtens\lacarte\fixtures\service\TimeFixture;
use spec\rtens\lacarte\TestCase;

/**
 * @property SessionFixture session
 * @property TimeFixture time
 * @property OrderFixture order
 * @property ListComponentFixture component
 */
class DisplayTodaysOrderTest extends TestCase {

    function testShowTodaysSelection() {
        $this->session->givenIAmLoggedAsTheUser('Homer');
        $this->time->givenNowIs('2013-04-04 18:00');
        $this->order->givenAnOrder_With_MenusEach_DishesStartingOn('order1', 1, 1, '2013-04-04');
        $this->order->givenDish_OfMenu_OfThisOrderIs(1, 1, 'Dish 1');
        $this->order->given_SelectedDish_ForMenu_OfOrder('Homer', 'Dish 1', 1, 'order1');

        $this->component->whenIOpenThePage();

        $this->component->thenItShouldDisplayTodaysOrder('Dish 1');
    }

    function testNoTodaysSelectionIfNotUser() {
        $this->session->givenIAmLoggedInAsAdmin();
        $this->time->givenNowIs('2013-04-04 18:00');
        $this->order->givenAnOrder_With_MenusEach_DishesStartingOn('order1', 1, 1, '2013-04-04');
        $this->order->givenDish_OfMenu_OfThisOrderIs(1, 1, 'Dish 1');

        $this->component->whenIOpenThePage();
        $this->component->thenThereShouldBeNoTodaysOrder();
    }

    function testNoSelectionForToday() {
        $this->session->givenIAmLoggedAsTheUser('Homer');
        $this->time->givenNowIs('2013-04-04 18:00');
        $this->order->givenAnOrder_With_MenusEach_DishesStartingOn('order1', 1, 1, '2013-04-04');
        $this->order->givenDish_OfMenu_OfThisOrderIs(1, 1, 'Dish 1');

        $this->component->whenIOpenThePage();

        $this->component->thenThereShouldBeNoTodaysOrder();
    }

    function testTodaysSelectionWhenSelectedNoDish() {
        $this->session->givenIAmLoggedAsTheUser('Homer');
        $this->time->givenNowIs('2013-04-04 18:00');
        $this->order->givenAnOrder_With_MenusEach_DishesStartingOn('order1', 1, 1, '2013-04-04');
        $this->order->given_SelectedNoDishForMenu_OfOrder('Homer', 1, 'order1');

        $this->component->whenIOpenThePage();
        $this->component->thenItShouldDisplayTodaysOrder('Nothing for you today.');
    }

    function testNoMenuForToday() {
        $this->session->givenIAmLoggedAsTheUser('Homer');
        $this->time->givenNowIs('2013-04-04 18:00');
        $this->order->givenAnOrder_With_MenusEach_DishesStartingOn('order1', 1, 1, '2013-04-05');

        $this->component->whenIOpenThePage();
        $this->component->thenThereShouldBeNoTodaysOrder();
    }
}