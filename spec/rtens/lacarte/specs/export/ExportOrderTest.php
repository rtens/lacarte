<?php
namespace spec\rtens\lacarte\specs\export;

use rtens\lacarte\web\export\DishesComponent;
use rtens\mockster\MockFactory;
use spec\rtens\lacarte\fixtures\component\export\DishesComponentFixture;
use spec\rtens\lacarte\fixtures\model\OrderFixture;
use spec\rtens\lacarte\fixtures\model\UserFixture;
use spec\rtens\lacarte\fixtures\service\SessionFixture;
use spec\rtens\lacarte\Specification;

/**
 * @property OrderFixture order <-
 * @property SessionFixture session <-
 * @property UserFixture user <-
 * @property DishesComponentFixture component <-
 */
class ExportOrderTest extends Specification {

    function testNotAdmin() {
        $this->order->givenAnOrder_With_MenusEach_Dishes('Test Order', 3, 2);

        $this->component->whenIExportTheOrder('Test Order');

        $this->component->thenIShouldBeRedirectedTo('../order/list.html');
    }

    function testNoSelections() {
        $this->session->givenIAmLoggedInAsAdmin();
        $this->order->givenAnOrder_With_MenusEach_DishesStartingOn('Test Order', 3, 2, '2000-01-03');
        $this->order->givenDish_OfMenu_OfThisOrderIs(1, 1, '1A');
        $this->order->givenDish_OfMenu_OfThisOrderIs(2, 1, '1B');
        $this->order->givenDish_OfMenu_OfThisOrderIs(1, 2, '2A');
        $this->order->givenDish_OfMenu_OfThisOrderIs(2, 2, '2B');
        $this->order->givenDish_OfMenu_OfThisOrderIs(1, 3, '3A');
        $this->order->givenDish_OfMenu_OfThisOrderIs(2, 3, '3B');

        $this->component->whenIExportTheOrder('Test Order');

        $this->component->thenThereShouldBe_Rows(6);
        $this->component->thenTheDateOfRow_ShouldBe(1, '2000-01-03');
        $this->component->thenTheDishOfRow_ShouldBe(1, '1A');
        $this->component->thenTheSumOfRow_ShouldBe(1, 0);
    }

    function testSelections() {
        $this->session->givenIAmLoggedInAsAdmin();
        $this->order->givenAnOrder_With_MenusEach_DishesStartingOn('Test Order', 1, 2, '2000-01-03');
        $this->order->givenDish_OfMenu_OfThisOrderIs(1, 1, 'A');
        $this->order->givenDish_OfMenu_OfThisOrderIs(2, 1, 'B');

        $this->user->givenTheUser('Bart');
        $this->user->givenTheUser('Lisa');
        $this->user->givenTheUser('Marge');
        $this->user->givenTheUser('Homer');

        $this->order->given_SelectedDish_ForMenu_OfOrder('Bart', 'A', 1, 'Test Order');
        $this->order->given_SelectedDish_ForMenu_OfOrder('Lisa', 'B', 1, 'Test Order');
        $this->order->given_SelectedDish_ForMenu_OfOrder('Marge', 'A', 1, 'Test Order');
        $this->order->given_SelectedNoDishForMenu_OfOrder('Homer', 1, 'Test Order');

        $this->component->whenIExportTheOrder('Test Order');

        $this->component->thenThereShouldBe_Rows(2);
        $this->component->thenTheDishOfRow_ShouldBe(1, 'A');
        $this->component->thenTheSumOfRow_ShouldBe(1, 2);
        $this->component->thenTheChoosersOfRow_ShouldBe(1, 'Bart, Marge');
        $this->component->thenTheDishOfRow_ShouldBe(2, 'B');
        $this->component->thenTheSumOfRow_ShouldBe(2, 1);
        $this->component->thenTheChoosersOfRow_ShouldBe(2, 'Lisa');
    }

    function testSelectionWithDeletedUser() {
        $this->session->givenIAmLoggedInAsAdmin();
        $this->order->givenAnOrder_With_MenusEach_Dishes('Test Order', 1, 1);
        $this->order->givenDish_OfMenu_OfThisOrderIs(1, 1, 'A');

        $this->user->givenTheUser('Bart');
        $this->user->givenTheUser('Lisa');

        $this->order->given_SelectedDish_ForMenu_OfOrder('Bart', 'A', 1, 'Test Order');
        $this->order->given_SelectedDish_ForMenu_OfOrder('Lisa', 'A', 1, 'Test Order');

        $this->user->given_WasDeleted('Bart');

        $this->component->whenIExportTheOrder('Test Order');

        $this->component->thenThereShouldBe_Rows(1);
        $this->component->thenTheChoosersOfRow_ShouldBe(1, 'Deleted, Lisa');
    }

}