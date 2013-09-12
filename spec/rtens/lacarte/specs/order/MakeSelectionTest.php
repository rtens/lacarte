<?php
namespace spec\rtens\lacarte\specs\order;

use rtens\mockster\MockFactory;
use spec\rtens\lacarte\fixtures\component\order\SelectComponentFixture;
use spec\rtens\lacarte\fixtures\model\OrderFixture;
use spec\rtens\lacarte\fixtures\model\SelectionFixture;
use spec\rtens\lacarte\fixtures\service\SessionFixture;
use spec\rtens\lacarte\fixtures\service\TimeFixture;
use spec\rtens\lacarte\fixtures\model\UserFixture;
use spec\rtens\lacarte\TestCase;

/**
 * @property TimeFixture time
 * @property SelectionFixture selection
 * @property OrderFixture order
 * @property SessionFixture session
 * @property UserFixture user
 * @property SelectComponentFixture component
 */
class MakeSelectionTest extends TestCase {

    function testAfterDeadline() {
        $this->time->givenNowIs('2000-01-01 18:00:01');
        $this->order->givenTheOrder_WithDeadline('Test Order', '2000-01-01 18:00');

        $this->component->whenIOpenThePageForOrder('Test Order');

        $this->component->thenIShouldBeRedirectedTo('selection.html?order=1');
    }

    function _testAdminAfterDeadline() {
        $this->time->givenNowIs('2000-01-01 19:12');
        $this->session->givenIAmLoggedInAsAdmin();
        $this->order->givenTheOrder_WithDeadline('Test Order', '2000-01-01 18:00');
        $this->user->givenTheUser('Bart');

        $this->component->whenIOpenThePageForOrderForTheUser('Test Order', 'Bart');

        $this->component->thenTheDisplayedTimeLeftShouldBe('-0d 1h 12m');
        $this->component->thenTheSelectionOf_ShouldBeLoaded('Bart');
    }

    function testNoSelection() {
        $this->time->givenNowIs('1999-12-30 15:38');
        $this->session->givenIAmLoggedAsTheUser('Bart');
        $this->order->givenAnOrder_WithDeadlineAnd_MenusEach_DishesStartingOn('Test Order', '2000-01-01 18:00', 2, 2, '2000-01-03');
        $this->order->givenDish_OfMenu_OfThisOrderIs(1, 1, 'A');
        $this->order->givenDish_OfMenu_OfThisOrderIs(2, 1, 'B');
        $this->order->givenDish_OfMenu_OfThisOrderIs(1, 2, 'C');
        $this->order->givenDish_OfMenu_OfThisOrderIs(2, 2, 'D');

        $this->component->whenIOpenThePageForOrder('Test Order');

        $this->component->thenThereShouldBeNoErrorMessage();
        $this->component->thenThereShouldBeNoSuccessMessage();
        $this->component->thenTheOrder_ShouldBeLoaded('Test Order');
        $this->component->thenTheDisplayedTimeLeftShouldBe('2d 2h 22m');
        $this->component->thenThereShouldBe_Menus(2);
        $this->component->thenTheDateOfMenu_ShouldBe(1, 'Monday, 3.1.2000');

        $this->component->thenTheNoneOptionOfMenu_ShouldNotBeChecked(1);
        $this->component->thenDish_OfMenu_ShouldNotBeChecked(1, 1);
        $this->component->thenDish_OfMenu_ShouldNotBeChecked(2, 1);
        $this->component->thenMenu_ShouldHave_Dishes(1, 2);
        $this->component->thenDish_OfMenu_ShouldBe(1, 1, 'A');
        $this->component->thenDish_OfMenu_ShouldBe(2, 1, 'B');

        $this->component->thenTheDateOfMenu_ShouldBe(2, 'Tuesday, 4.1.2000');
    }

    function testCreateSelection() {
        $this->session->givenIAmLoggedAsTheUser('Bart');
        $this->order->givenAnOrder_With_MenusEach_Dishes('Test Order', 3, 2);
        $this->order->givenDish_OfMenu_OfThisOrderIs(1, 1, 'A');
        $this->order->givenDish_OfMenu_OfThisOrderIs(2, 1, 'B');
        $this->order->givenDish_OfMenu_OfThisOrderIs(1, 2, 'C');
        $this->order->givenDish_OfMenu_OfThisOrderIs(2, 2, 'D');
        $this->order->givenDish_OfMenu_OfThisOrderIs(1, 3, 'E');
        $this->order->givenDish_OfMenu_OfThisOrderIs(2, 3, 'F');

        $this->component->givenIHaveOpenedThePageForOrder('Test Order');
        $this->component->givenISelectedDish_OfMenu('A', 1);
        $this->component->givenISelectedDish_OfMenu('D', 2);
        $this->component->givenISelectedNoDishOfMenu(3);

        $this->component->whenISaveMySelections();

        $this->selection->thenThereShouldBe_Selections(3);
        $this->selection->thenThereShouldBeASelectionWithMenu_OfOrder_AndDish_ForUser(1, 'Test Order', 'A', 'Bart');
        $this->selection->thenThereShouldBeASelectionWithMenu_OfOrder_AndDish_ForUser(2, 'Test Order', 'D', 'Bart');
        $this->selection->thenThereShouldBeASelectionWithMenu_OfOrder_AndNoDishForUser(3, 'Test Order', 'Bart');

        $this->component->thenTheSuccessMessageShouldBe('Selection saved');
    }

    function testLoadSelections() {
        $this->session->givenIAmLoggedAsTheUser('Bart');
        $this->time->givenNowIs('2000-01-01 17:59');
        $this->order->givenAnOrder_WithDeadlineAnd_MenusEach_DishesStartingOn('Test Order', '2000-01-01 18:00', 3, 2, '2000-01-03');
        $this->order->givenDish_OfMenu_OfThisOrderIs(1, 1, 'A');
        $this->order->givenDish_OfMenu_OfThisOrderIs(2, 1, 'B');
        $this->order->givenDish_OfMenu_OfThisOrderIs(1, 2, 'C');
        $this->order->givenDish_OfMenu_OfThisOrderIs(2, 2, 'D');
        $this->order->givenDish_OfMenu_OfThisOrderIs(1, 3, 'E');
        $this->order->givenDish_OfMenu_OfThisOrderIs(2, 3, 'F');
        $this->order->given_SelectedNoDishForMenu_OfOrder('Bart', 1, 'Test Order');
        $this->order->given_SelectedDish_ForMenu_OfOrder('Bart', 'C', 2, 'Test Order');

        $this->component->whenIOpenThePageForOrder('Test Order');

        $this->component->thenThereShouldBe_Menus(3);
        $this->component->thenMenu_ShouldHave_Dishes(1, 2);
        $this->component->thenTheNoneOptionOfMenu_ShouldBeChecked(1);
        $this->component->thenDish_OfMenu_ShouldNotBeChecked(1, 1);
        $this->component->thenDish_OfMenu_ShouldNotBeChecked(2, 1);

        $this->component->thenMenu_ShouldHave_Dishes(2, 2);
        $this->component->thenTheNoneOptionOfMenu_ShouldNotBeChecked(2);
        $this->component->thenDish_OfMenu_ShouldBeChecked(1, 2);
        $this->component->thenDish_OfMenu_ShouldNotBeChecked(2, 2);

        $this->component->thenMenu_ShouldHave_Dishes(3, 2);
        $this->component->thenTheNoneOptionOfMenu_ShouldNotBeChecked(3);
        $this->component->thenDish_OfMenu_ShouldNotBeChecked(1, 3);
        $this->component->thenDish_OfMenu_ShouldNotBeChecked(2, 3);
    }

    function testUpdateSelections() {
        $this->session->givenIAmLoggedAsTheUser('Bart');
        $this->time->givenNowIs('2000-01-01 17:59');
        $this->order->givenAnOrder_WithDeadlineAnd_MenusEach_DishesStartingOn('Test Order', '2000-01-01 18:00', 3, 2, '2000-01-03');
        $this->order->givenDish_OfMenu_OfThisOrderIs(1, 1, 'A');
        $this->order->givenDish_OfMenu_OfThisOrderIs(2, 1, 'B');
        $this->order->givenDish_OfMenu_OfThisOrderIs(1, 2, 'C');
        $this->order->givenDish_OfMenu_OfThisOrderIs(2, 2, 'D');
        $this->order->givenDish_OfMenu_OfThisOrderIs(1, 3, 'E');
        $this->order->givenDish_OfMenu_OfThisOrderIs(2, 3, 'F');
        $this->order->given_SelectedDish_ForMenu_OfOrder('Bart', 'A', 1, 'Test Order');
        $this->order->given_SelectedDish_ForMenu_OfOrder('Bart', 'C', 2, 'Test Order');
        $this->order->given_SelectedNoDishForMenu_OfOrder('Bart', 3, 'Test Order');

        $this->component->givenIHaveOpenedThePageForOrder('Test Order');
        $this->component->givenISelectedNoDishOfMenu(1);
        $this->component->givenISelectedDish_OfMenu('D', 2);
        $this->component->givenISelectedDish_OfMenu('F', 3);

        $this->component->whenISaveMySelections();

        $this->selection->thenThereShouldBe_Selections(3);
        $this->selection->thenThereShouldBeASelectionWithMenu_OfOrder_AndNoDishForUser(1, 'Test Order', 'Bart');
        $this->selection->thenThereShouldBeASelectionWithMenu_OfOrder_AndDish_ForUser(2, 'Test Order', 'D', 'Bart');
        $this->selection->thenThereShouldBeASelectionWithMenu_OfOrder_AndDish_ForUser(3, 'Test Order', 'F', 'Bart');
    }

    function testNotAllSelected() {
        $this->session->givenIAmLoggedAsTheUser('Bart');
        $this->time->givenNowIs('2000-01-01 17:59');
        $this->order->givenAnOrder_WithDeadlineAnd_MenusEach_DishesStartingOn('Test Order', '2000-01-01 18:00', 3, 2, '2000-01-03');
        $this->order->givenDish_OfMenu_OfThisOrderIs(1, 1, 'A');
        $this->order->givenDish_OfMenu_OfThisOrderIs(2, 1, 'B');
        $this->order->givenDish_OfMenu_OfThisOrderIs(1, 2, 'C');
        $this->order->givenDish_OfMenu_OfThisOrderIs(2, 2, 'D');
        $this->order->givenDish_OfMenu_OfThisOrderIs(1, 3, 'E');
        $this->order->givenDish_OfMenu_OfThisOrderIs(2, 3, 'F');

        $this->component->givenIHaveOpenedThePageForOrder('Test Order');
        $this->component->givenISelectedNoDishOfMenu(1);
        $this->component->givenISelectedDish_OfMenu('F', 3);

        $this->component->whenISaveMySelections();

        $this->selection->thenThereShouldBe_Selections(0);

        $this->component->thenTheErrorMessageShouldBe('Please make a selection for every day');

        $this->component->thenTheNoneOptionOfMenu_ShouldBeChecked(1);
        $this->component->thenDish_OfMenu_ShouldNotBeChecked(1, 1);
        $this->component->thenDish_OfMenu_ShouldNotBeChecked(2, 1);
        $this->component->thenTheNoneOptionOfMenu_ShouldNotBeChecked(2);
        $this->component->thenDish_OfMenu_ShouldNotBeChecked(1, 2);
        $this->component->thenDish_OfMenu_ShouldNotBeChecked(2, 2);
        $this->component->thenTheNoneOptionOfMenu_ShouldNotBeChecked(3);
        $this->component->thenDish_OfMenu_ShouldNotBeChecked(1, 3);
        $this->component->thenDish_OfMenu_ShouldBeChecked(2, 3);
    }

    function _testNothingSelected() {
        $this->session->givenIAmLoggedAsTheUser('Bart');
        $this->time->givenNowIs('2000-01-01 17:59');
        $this->order->givenAnOrder_WithDeadlineAnd_MenusEach_DishesStartingOn('Test Order', '2000-01-01 18:00', 3, 2, '2000-01-03');

        $this->component->givenIHaveOpenedThePageForOrder('Test Order');

        $this->component->whenISaveMySelections();

        $this->component->thenTheErrorMessageShouldBe('Please make a selection for every day');
    }

}