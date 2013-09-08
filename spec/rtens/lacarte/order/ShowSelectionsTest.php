<?php
namespace spec\rtens\lacarte\order;

use rtens\mockster\MockFactory;
use spec\rtens\lacarte\fixture\component\order\SelectionsComponentFixture;
use spec\rtens\lacarte\fixture\model\OrderFixture;
use spec\rtens\lacarte\fixture\model\SessionFixture;
use spec\rtens\lacarte\fixture\model\UserFixture;
use spec\rtens\lacarte\TestCase;

class ShowSelectionsTest extends TestCase {

    /** @var UserFixture */
    private $user;

    /** @var SessionFixture */
    private $session;

    /** @var SelectionsComponentFixture */
    private $component;

    /** @var OrderFixture */
    private $order;

    protected function setUp() {
        parent::setUp();

        $this->order = $this->useFixture(OrderFixture::$CLASS);
        $this->user = $this->useFixture(UserFixture::$CLASS);
        $this->session = $this->useFixture(SessionFixture::$CLASS);
        $this->component = $this->useFixture(SelectionsComponentFixture::$CLASS);
    }

    function testNotAdmin() {
        $this->order->givenAnOrder_With_MenusEach_Dishes('Test Order', 0, 0);
        $this->component->whenIOpenThePageForOrder('Test Order');
        $this->component->thenIShouldBeRedirectedTo('list.html');
    }

    function testNoUsers() {
        $this->session->givenIAmLoggedInAsAdmin();
        $this->order->givenAnOrder_With_MenusEach_DishesStartingOn('Test Order', 3, 2, '2000-01-03');

        $this->component->whenIOpenThePageForOrder('Test Order');

        $this->component->thenThereShouldBeNoSuccessMessage();
        $this->component->thenThereShouldBeNoErrorMessage();

        $this->component->thenTheEditActionShouldGoTo('edit.html?order=1');
        $this->component->thenTheExportByDishActionShouldGoTo('../export/dishes.csv?order=1');
        $this->component->thenThereShouldBeNoExportByUserAction();

        $this->component->thenNameOfTheOrderShouldBe('Test Order');

        $this->component->thenTheOrderShouldHave_Dates(3);
        $this->component->thenDate_ShouldBe(1, 'Mon');

        $this->component->thenThereShouldBe_Users(0);
    }

    function testNoSelections() {
        $this->session->givenIAmLoggedInAsAdmin();

        $this->order->givenAnOrder_With_MenusEach_Dishes('Test Order', 3, 2);

        $this->user->givenTheUser('Bart Simpson');
        $this->user->givenTheUser('Lisa Simpson');
        $this->user->givenTheUser('Maggie Simpson');

        $this->component->whenIOpenThePageForOrder('Test Order');

        $this->component->thenThereShouldBe_Users(3);
        $this->component->thenTheNameOfUser_ShouldBe(1, 'Bart Simpson');
        $this->component->thenTheSelectionLinkOfUser_ShouldBe(1, 'select.html?order=1&user=1');
        $this->component->thenUser_ShouldHave_Selections(1, 3);
        $this->component->thenUser_ShouldHaveNothingSelectedForSelection(1, 1);
        $this->component->thenUser_ShouldHaveNothingSelectedForSelection(1, 2);
        $this->component->thenUser_ShouldHaveNothingSelectedForSelection(1, 3);

        $this->component->thenTheNameOfUser_ShouldBe(2, 'Lisa Simpson');
        $this->component->thenTheNameOfUser_ShouldBe(3, 'Maggie Simpson');
    }

    function testUsersWithSelections() {
        $this->session->givenIAmLoggedInAsAdmin();
        $this->order->givenAnOrder_With_MenusEach_Dishes('Test Order', 1, 2);
        $this->order->givenDish_OfMenu_OfThisOrderIs(1, 1, 'Dish One');
        $this->order->givenDish_OfMenu_OfThisOrderIs(2, 1, 'Dish Two');

        $this->user->givenTheUser('Bart');
        $this->user->givenTheUser('Lisa');
        $this->user->givenTheUser('Maggie');

        $this->order->given_SelectedDish_ForMenu_OfOrder('Bart', 'Dish One', 1, 'Test Order');
        $this->order->given_SelectedDish_ForMenu_OfOrder('Lisa', 'Dish Two', 1, 'Test Order');
        $this->order->given_SelectedNoDishForMenu_OfOrder('Maggie', 1, 'Test Order');

        $this->component->whenIOpenThePageForOrder('Test Order');

        $this->component->thenTheSelectionOfUser_ShouldBe_WithTheTitle(1, 'Dish...', 'Dish One');
        $this->component->thenTheSelectionOfUser_ShouldBe_WithTheTitle(2, 'Dish...', 'Dish Two');
        $this->component->thenTheSelectionOfUser_ShouldBe_WithTheTitle(3, '-', 'nothing');
    }

}