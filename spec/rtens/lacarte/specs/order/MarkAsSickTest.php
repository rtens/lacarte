<?php
namespace spec\rtens\lacarte\specs\order;

use spec\rtens\lacarte\fixtures\model\OrderFixture;
use spec\rtens\lacarte\fixtures\resource\order\SelectionFixture;
use spec\rtens\lacarte\fixtures\service\SessionFixture;
use spec\rtens\lacarte\Specification;

/**
 * @property OrderFixture order <-
 * @property SessionFixture session <-
 * @property SelectionFixture res <-
 */
class MarkAsSickTest extends Specification {

    protected function background() {
        $this->session->givenIAmLoggedAsTheUser('Bart');
        $this->order->givenAnOrder_With_MenusEach_DishesStartingOn('Test Order', 3, 2, '2000-01-03');
        $this->order->givenDish_OfMenu_OfThisOrderIs(1, 1, 'Dish One A');
        $this->order->givenDish_OfMenu_OfThisOrderIs(2, 1, 'Dish One B');
        $this->order->givenDish_OfMenu_OfThisOrderIs(1, 2, 'Dish Two A');
        $this->order->givenDish_OfMenu_OfThisOrderIs(2, 2, 'Dish Two B');
        $this->order->givenDish_OfMenu_OfThisOrderIs(1, 3, 'Dish Three A');
        $this->order->givenDish_OfMenu_OfThisOrderIs(2, 3, 'Dish Three B');
        $this->order->given_SelectedDish_ForMenu_OfOrder('Bart', 'Dish One A', 1, 'Test Order');
        $this->order->given_SelectedDish_ForMenu_OfOrder('Bart', 'Dish Two B', 2, 'Test Order');
        $this->order->given_SelectedNoDishForMenu_OfOrder('Bart', 3, 'Test Order');
    }

    function testShowActions() {
        $this->order->given_YieldedHisSelectionOfMenu_OfOrder('Bart', 1, 'Test Order');

        $this->res->whenIOpenThePageForOrder('Test Order');

        $this->res->thenThereShouldBeNoErrorMessage();

        $this->res->thenSelection_ShouldBeUnYieldable(1);
        $this->res->thenSelection_ShouldBeYieldable(2);
        $this->res->thenSelection_ShouldNotBeYieldableNorUnYieldable(3);
    }

    function testNotLoggedIn() {
        $this->markTestIncomplete();
    }

    function testSucceed() {
        $this->markTestIncomplete();
    }

    function testTooLate() {
        $this->markTestIncomplete();
    }

} 