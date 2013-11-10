<?php
namespace spec\rtens\lacarte\specs\order;

use spec\rtens\lacarte\fixtures\model\OrderFixture;
use spec\rtens\lacarte\fixtures\model\UserFixture;
use spec\rtens\lacarte\fixtures\resource\selections\SelectionResourceFixture;
use spec\rtens\lacarte\Specification;

/**
 * @property SelectionResourceFixture res <-
 * @property OrderFixture order <-
 * @property UserFixture user <-
 */
class ChangeYieldedOfSelectionTest extends Specification {

    function testInvalidId() {
        $this->res->whenISetYieldedOfSelection_To(42, true);
        $this->res->thenTheErrorShouldContain('Selection with ID [42] not found');
    }

    function testSetYielded() {
        $this->user->givenTheUser('Bart');
        $this->order->givenAnOrder_With_MenusEach_Dishes('Order', 1, 1);
        $this->order->givenDish_OfMenu_OfThisOrderIs(1, 1, 'A');
        $this->order->given_SelectedDish_ForMenu_OfOrder('Bart', 'A', 1, 'Order');

        $this->res->whenISetYieldedOfSelection_To(1, true);

        $this->res->thenTheResponseShouldBeEmpty();
        $this->order->thenThereShouldBe_Selections(1);
        $this->order->thenSelection_ShouldBeYielded(1);
    }

} 