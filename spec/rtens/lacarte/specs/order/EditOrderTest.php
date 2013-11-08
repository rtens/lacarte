<?php
namespace spec\rtens\lacarte\specs\order;

use spec\rtens\lacarte\fixtures\model\OrderFixture;
use spec\rtens\lacarte\fixtures\resource\order\EditResourceFixture;
use spec\rtens\lacarte\fixtures\service\SessionFixture;
use spec\rtens\lacarte\Specification;

/**
 * @property OrderFixture order <-
 * @property SessionFixture session <-
 * @property EditResourceFixture component <-
 */
class EditOrderTest extends Specification {

    function testNotAdminWhenOpeningThePage() {
        $this->order->givenAnOrder_With_MenusEach_Dishes('Test Order', 2, 2);

        $this->component->whenIOpenThePageToEdit('Test Order');

        $this->component->thenIShouldBeRedirectedTo('list.html');
    }

    function testNotAdminWhenSaving() {
        $this->order->givenAnOrder_With_MenusEach_Dishes('Test Order', 2, 2);
        $this->component->givenIHaveOpenedThePageToEdit('Test Order');

        $this->component->whenISaveTheOrder();

        $this->component->thenIShouldBeRedirectedTo('list.html');
    }

    function testLoadDishes() {
        $this->session->givenIAmLoggedInAsAdmin();
        $this->order->givenAnOrder_With_MenusEach_DishesStartingOn('Test Order', 2, 2, '2000-01-03');
        $this->order->givenDish_OfMenu_OfThisOrderIs(1, 1, 'A');
        $this->order->givenDish_OfMenu_OfThisOrderIs(2, 1, 'B');
        $this->order->givenDish_OfMenu_OfThisOrderIs(1, 2, 'C');
        $this->order->givenDish_OfMenu_OfThisOrderIs(2, 2, 'D');

        $this->component->whenIOpenThePageToEdit('Test Order');

        $this->component->thenThereShouldBeNoErrorMessage();
        $this->component->thenThereShouldBeNoSuccessMessage();

        $this->component->thenTheNameOfTheOrderShouldBe('Test Order');

        $this->component->thenThereShouldBe_Menus(2);
        $this->component->thenTheDateOfMenu_ShouldBe(1, 'Monday, 3.1.2000');
        $this->component->thenTheDateOfMenu_ShouldBe(2, 'Tuesday, 4.1.2000');
        $this->component->thenMenu_ShouldHave_Dishes(1, 2);
        $this->component->thenDish_OfMenu_ShouldBe(1, 1, 'A');
        $this->component->thenDish_OfMenu_ShouldBe(2, 1, 'B');
        $this->component->thenDish_OfMenu_ShouldBe(1, 2, 'C');
        $this->component->thenDish_OfMenu_ShouldBe(2, 2, 'D');
    }

    function testSaveDishes() {
        $this->session->givenIAmLoggedInAsAdmin();
        $this->order->givenAnOrder_With_MenusEach_DishesStartingOn('Test Order', 2, 2, '2000-01-03');

        $this->component->givenIHaveOpenedThePageToEdit('Test Order');
        $this->component->givenIHaveEntered_ForDish_OfMenu('W', 1, 1);
        $this->component->givenIHaveEntered_ForDish_OfMenu('X', 2, 1);
        $this->component->givenIHaveEntered_ForDish_OfMenu('Y', 1, 2);
        $this->component->givenIHaveEntered_ForDish_OfMenu('Z', 2, 2);

        $this->component->whenISaveTheOrder();

        $this->order->thenThereShouldBe_Orders(1);
        $this->order->thenThereShouldBe_Dishes(4);
        $this->order->thenThereShouldBeADish('W');
        $this->order->thenThereShouldBeADish('X');
        $this->order->thenThereShouldBeADish('Y');
        $this->order->thenThereShouldBeADish('Z');

        $this->component->thenTheSuccessMessageShouldBe('Order saved');
        $this->component->thenThereShouldBeNoErrorMessage();
        $this->component->thenTheNameOfTheOrderShouldBe('Test Order');
    }

    function testDiscardEmptyDishes() {
        $this->session->givenIAmLoggedInAsAdmin();
        $this->order->givenAnOrder_With_MenusEach_DishesStartingOn('Test Order', 2, 3, '2000-01-03');

        $this->component->givenIHaveOpenedThePageToEdit('Test Order');
        $this->component->givenIHaveEntered_ForDish_OfMenu('W', 1, 1);
        $this->component->givenIHaveEntered_ForDish_OfMenu('X', 2, 1);
        $this->component->givenIHaveEntered_ForDish_OfMenu('', 3, 1);
        $this->component->givenIHaveEntered_ForDish_OfMenu('Y', 1, 2);
        $this->component->givenIHaveEntered_ForDish_OfMenu('', 2, 2);
        $this->component->givenIHaveEntered_ForDish_OfMenu('', 3, 2);

        $this->component->whenISaveTheOrder();

        $this->order->thereShouldBe_Menus(2);
        $this->order->thenThereShouldBe_Dishes(3);
    }

    function testDiscardEmptyMenu() {
        $this->session->givenIAmLoggedInAsAdmin();
        $this->order->givenAnOrder_With_MenusEach_DishesStartingOn('Test Order', 2, 3, '2000-01-03');

        $this->component->givenIHaveOpenedThePageToEdit('Test Order');
        $this->component->givenIHaveEntered_ForDish_OfMenu('W', 1, 1);
        $this->component->givenIHaveEntered_ForDish_OfMenu('X', 2, 1);
        $this->component->givenIHaveEntered_ForDish_OfMenu('', 3, 1);
        $this->component->givenIHaveEntered_ForDish_OfMenu('', 1, 2);
        $this->component->givenIHaveEntered_ForDish_OfMenu('', 2, 2);
        $this->component->givenIHaveEntered_ForDish_OfMenu('', 3, 2);

        $this->component->whenISaveTheOrder();

        $this->order->thenThisOrderShouldHave_Menus(1);
        $this->order->thenThereShouldBe_Dishes(2);
    }

}