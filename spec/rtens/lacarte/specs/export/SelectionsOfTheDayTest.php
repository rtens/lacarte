<?php
namespace spec\rtens\lacarte\specs\export;

use spec\rtens\lacarte\fixtures\component\export\SelectionsComponentFixture;
use spec\rtens\lacarte\fixtures\model\OrderFixture;
use spec\rtens\lacarte\fixtures\model\UserFixture;
use spec\rtens\lacarte\fixtures\service\ConfigFixture;
use spec\rtens\lacarte\fixtures\service\FileFixture;
use spec\rtens\lacarte\fixtures\service\TimeFixture;
use spec\rtens\lacarte\TestCase;

class SelectionsOfTheDayTest extends TestCase {

    /** @var ConfigFixture */
    public $config;

    /** @var SelectionsComponentFixture */
    public $component;

    /** @var OrderFixture */
    public $order;

    /** @var UserFixture */
    public $user;

    /** @var TimeFixture */
    public $time;

    /** @var FileFixture */
    public $file;

    public function setUp() {
        parent::setUp();

        $this->config = $this->useFixture(ConfigFixture::$CLASS);
        $this->order = $this->useFixture(OrderFixture::$CLASS);
        $this->user = $this->useFixture(UserFixture::$CLASS);
        $this->time = $this->useFixture(TimeFixture::$CLASS);
        $this->file = $this->useFixture(FileFixture::$CLASS);
        $this->component = $this->useFixture(SelectionsComponentFixture::$CLASS);

        $this->background();
    }

    public function background() {
        $this->config->givenTheApiTokenIs('token');
    }

    function testNoMenu() {
        $this->component->whenIOpenTheSelectionsOf_WithToken('2013-01-04', 'token');

        $this->component->thenTheMenuShouldBeEmpty();
        $this->component->thenThereShouldBe_Selections(0);
        $this->component->thenTheErrorMessageShouldBe('No menu found for given date.');
    }

    function testWrongDateFormat() {
        $this->component->whenIOpenTheSelectionsOf_WithToken('not a date', 'token');
        $this->component->thenTheErrorMessageShouldBe('Could not parse date.');
    }

    function testWrongToken() {
        $this->component->whenIOpenTheSelectionsOf_WithToken('2013-01-04', 'not the token');
        $this->component->thenTheErrorMessageShouldBe('Wrong token.');
    }

    function testNoSelections() {
        $this->order->givenAnOrder_With_MenusEach_DishesStartingOn('Test Order', 1, 3, '2000-01-03');
        $this->order->givenDish_OfMenu_OfThisOrderIs(1, 1, 'German Text / English Text');
        $this->order->givenDish_OfMenu_OfThisOrderIs(2, 1, 'Only english Text');
        $this->order->givenDish_OfMenu_OfThisOrderIs(3, 1, 'Something else');

        $this->component->whenIOpenTheSelectionsOf_WithToken('2000-01-03', 'token');

        $this->component->thenTheDateShouldBe('2000-01-03');
        $this->component->thenThereShouldBe_Dishes(3);
        $this->component->thenDish_ShouldBe_InEnglish(1, 'English Text');
        $this->component->thenDish_ShouldBe_InGerman(1, 'German Text');
        $this->component->thenDish_ShouldBe_InEnglish(2, 'Only english Text');
        $this->component->thenDish_ShouldBe_InGerman(2, 'Only english Text');

        $this->component->thenThereShouldBe_Selections(0);
    }

    function testAllSelections() {
        $this->order->givenAnOrder_With_MenusEach_DishesStartingOn('Test Order', 1, 3, '2000-01-03');
        $this->order->givenDish_OfMenu_OfThisOrderIs(1, 1, 'A');
        $this->order->givenDish_OfMenu_OfThisOrderIs(2, 1, 'B');
        $this->order->givenDish_OfMenu_OfThisOrderIs(3, 1, 'C');

        $this->user->givenTheUser('Bart');
        $this->user->givenTheUser('Lisa');
        $this->user->givenTheUser('Maggie');

        $this->order->given_SelectedDish_ForMenu_OfOrder('Bart', 'A', 1, 'Test Order');
        $this->order->given_SelectedDish_ForMenu_OfOrder('Lisa', 'B', 1, 'Test Order');
        $this->order->given_SelectedNoDishForMenu_OfOrder('Lisa', 1, 'Test Order');

        $this->component->whenIOpenTheSelectionsOf_WithToken('2000-01-03', 'token');
        $this->component->thenThereShouldBe_Selections(2);
        $this->component->thenSelection_ShouldBeOfUser_ForDish(1, 'Bart', 'A');
        $this->component->thenSelection_ShouldBeOfUser_ForDish(2, 'Lisa', 'B');
    }

    function testDefaultDate() {
        $this->order->givenAnOrder_With_MenusEach_DishesStartingOn('Test Order', 1, 1, '2000-01-03');
        $this->order->givenDish_OfMenu_OfThisOrderIs(1, 1, 'A');
        $this->time->givenNowIs('2000-01-03');

        $this->component->whenIOpenTheSelectionsWithToke('token');

        $this->component->thenDish_ShouldBe_InEnglish(1, 'A');
    }

    function testNoSelection() {
        $this->order->givenAnOrder_With_MenusEach_DishesStartingOn('Test Order', 1, 1, '2000-01-03');
        $this->order->givenDish_OfMenu_OfThisOrderIs(1, 1, 'A');

        $this->user->givenTheUser('Bart');
        $this->user->givenTheUser('Lisa');

        $this->order->given_SelectedDish_ForMenu_OfOrder('Bart', 'A', 1, 'Test Order');

        $this->component->whenIOpenTheSelectionsOf_WithToken('2000-01-03', 'token');

        $this->component->thenThereShouldBe_Selections(1);
    }

    function testAvatars() {
        $this->order->givenAnOrder_With_MenusEach_DishesStartingOn('Test Order', 1, 1, '2000-01-03');
        $this->order->givenDish_OfMenu_OfThisOrderIs(1, 1, 'A');

        $this->user->givenTheUser('Bart');
        $this->user->givenTheUser('Lisa');

        $this->order->given_SelectedDish_ForMenu_OfOrder('Bart', 'A', 1, 'Test Order');
        $this->order->given_SelectedDish_ForMenu_OfOrder('Lisa', 'A', 1, 'Test Order');

        $this->file->given_HasAnAvatar('Lisa');

        $this->component->whenIOpenTheSelectionsOf_WithToken('2000-01-03', 'token');

        $this->component->thenTheAvatarOfTheUserOfSelection_ShouldBe(1, 'http://lacarte/user/avatars/default.png');
        $this->component->thenTheAvatarOfTheUserOfSelection_ShouldBe(2, 'http://lacarte/user/avatars/2.jpg');
    }

}