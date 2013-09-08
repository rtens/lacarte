<?php
namespace spec\rtens\lacarte\order;

use rtens\mockster\MockFactory;
use spec\rtens\lacarte\fixture\component\order\SelectionComponentFixture;
use spec\rtens\lacarte\fixture\model\OrderFixture;
use spec\rtens\lacarte\fixture\service\SessionFixture;
use spec\rtens\lacarte\TestCase;

class ShowSelectionTest extends TestCase {

    /** @var SelectionComponentFixture */
    public $component;

    /** @var OrderFixture */
    private $order;

    /** @var SessionFixture */
    private $session;

    protected function setUp() {
        parent::setUp();

        $this->session = $this->useFixture(SessionFixture::$CLASS);
        $this->order = $this->useFixture(OrderFixture::$CLASS);
        $this->component = $this->useFixture(SelectionComponentFixture::$CLASS);
    }

    function testNoSelection() {
        $this->session->givenIAmLoggedAsTheUser('Bart');
        $this->order->givenAnOrder_With_MenusEach_Dishes('Test Order', 1, 1);

        $this->component->whenIOpenThePageForOrder('Test Order');

        $this->component->thenTheErrorMessageShouldBe('You seem to have no selections for this order.');
    }

    function testMixedSelections() {
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

        $this->component->whenIOpenThePageForOrder('Test Order');

        $this->component->thenThereShouldBeNoErrorMessage();
        $this->component->thenTheNameOfTheOrderShouldBe('Test Order');
        $this->component->thenThereShouldBe_Selections(3);
        $this->component->thenTheDateOfSelection_ShouldBe(1, 'Monday, 3.1.2000');
        $this->component->thenTheSelectedDishOfSelection_ShouldBe(1, 'Dish One A');
        $this->component->thenTheDateOfSelection_ShouldBe(2, 'Tuesday, 4.1.2000');
        $this->component->thenTheSelectedDishOfSelection_ShouldBe(2, 'Dish Two B');
        $this->component->thenTheDateOfSelection_ShouldBe(3, 'Wednesday, 5.1.2000');
        $this->component->thenTheSelectedDishOfSelection_ShouldBe(3, 'You selected no dish');

        $this->component->thenSelection_ShouldHave_NotSelectedDish(1, 1);
        $this->component->thenNotSelectedDish_OfSelection_ShouldBe(1, 1, 'Dish One B');
        $this->component->thenSelection_ShouldHave_NotSelectedDish(2, 1);
        $this->component->thenNotSelectedDish_OfSelection_ShouldBe(1, 2, 'Dish Two A');
        $this->component->thenSelection_ShouldHave_NotSelectedDish(3, 2);
        $this->component->thenNotSelectedDish_OfSelection_ShouldBe(1, 3, 'Dish Three A');
        $this->component->thenNotSelectedDish_OfSelection_ShouldBe(2, 3, 'Dish Three B');
    }

    function testAsAdmin() {
        $this->session->givenIAmLoggedInAsAdmin();
        $this->order->givenAnOrder_With_MenusEach_Dishes('Test Order', 1, 1);

        $this->component->whenIOpenThePageForOrder('Test Order');

        $this->component->thenIShouldBeRedirectedTo('selections.html?order=1');
    }

}