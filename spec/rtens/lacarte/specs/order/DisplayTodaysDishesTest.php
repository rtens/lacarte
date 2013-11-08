<?php


namespace spec\rtens\lacarte\specs\order;


use spec\rtens\lacarte\fixtures\component\order\ListComponentFixture;
use spec\rtens\lacarte\fixtures\component\order\TodaysDishesFixture;
use spec\rtens\lacarte\fixtures\model\OrderFixture;
use spec\rtens\lacarte\fixtures\service\SessionFixture;
use spec\rtens\lacarte\fixtures\service\TimeFixture;
use spec\rtens\lacarte\Specification;

/**
 * @property SessionFixture session <-
 * @property TimeFixture time <-
 * @property OrderFixture order <-
 * @property TodaysDishesFixture component <-
 */
class DisplayTodaysDishesTest extends Specification {

    function testShowTodaysDishes() {
        $this->session->givenIAmLoggedAsTheUser('Homer');
        $this->time->givenNowIs('2013-04-04 18:00:00');
        $this->order->givenAnOrder_With_MenusEach_DishesStartingOn('order1', 1, 3, '2013-04-04');
        $this->order->givenDish_OfMenu_OfThisOrderIs(1, 1, 'Dish 1');
        $this->order->givenDish_OfMenu_OfThisOrderIs(2, 1, 'Dish 2');
        $this->order->givenDish_OfMenu_OfThisOrderIs(3, 1, 'Dish 3');

        $this->component->whenIOpenThePage();

        $this->component->thenThereShouldBe_Dishes('3');
    }
}