<?php
namespace spec\rtens\lacarte\export;

use spec\rtens\lacarte\TestCase;

class SelectionsOfDayTest extends TestCase {

    public function setUp() {
        parent::setUp();
        $this->config->__mock()->method('getApiToken')->willReturn('token');
    }

    function _testNoMenu() {
        $this->when->iRequestTheSelectionsFor_WithTheToken('2013-01-04', 'token');

        $this->then->_shouldHaveTheSize('menu', 0);
        $this->then->_shouldHaveTheSize('selections', 0);
        $this->then->_shouldBe('error', 'No menu found for given date.');
    }

    function _testWrongDateFormat() {
        $this->when->iRequestTheSelectionsFor_WithTheToken('not a date', 'token');
        $this->then->_shouldBe('error', 'Could not parse date.');
    }

    function _testWrongToken() {
        $this->when->iRequestTheSelectionsFor_WithTheToken('2013-01-04', 'not the token');
        $this->then->_shouldBe('error', 'Wrong token.');
    }

    function _testNoSelections() {
        $this->given->anOrder_With_MenusEach_Dishes('Test', 1, 3);
        $this->given->dish_OfMenu_Is(1, 1, 'German Text / English Text');
        $this->given->dish_OfMenu_Is(2, 1, ' Only english Text');
        $this->given->dish_OfMenu_Is(3, 1, 'Something Else');

        $this->when->iRequestTheSelectionsFor_WithTheToken('2000-01-03', 'token');

        $this->then->_shouldBe('menu/date', '2000-01-03');
        $this->then->_shouldHaveTheSize('menu/dishes', 3);
        $this->then->_shouldBe('menu/dishes/1/en', 'English Text');
        $this->then->_shouldBe('menu/dishes/1/de', 'German Text');
        $this->then->_shouldBe('menu/dishes/2/en', 'Only english Text');
        $this->then->_shouldBe('menu/dishes/2/de', 'Only english Text');

        $this->then->_shouldHaveTheSize('selections', 0);
    }

    function _testAllSelections() {
        $this->given->anOrder_With_MenusEach_Dishes('Test', 1, 3);
        $this->given->dish_OfMenu_Is(1, 1, 'German Text/English Text');
        $this->given->dish_OfMenu_Is(2, 1, 'Only english Text');
        $this->given->dish_OfMenu_Is(3, 1, 'Something Else');

        $this->given->theUser('Tick');
        $this->given->theUser('Trick');
        $this->given->theUser('Track');

        $this->given->_SelectedDish_ForMenu('Tick', 1, 1);
        $this->given->_SelectedDish_ForMenu('Trick', 2, 1);
        $this->given->_SelectedDish_ForMenu('Track', 0, 1);

        $this->when->iRequestTheSelectionsFor_WithTheToken('2000-01-03', 'token');

        $this->then->_shouldHaveTheSize('selections', 2);

        $this->then->_shouldBe('selections/141/user/id', 41);
        $this->then->_shouldBe('selections/141/user/name', "Tick");
        $this->then->_shouldBe('selections/141/dish', 1);

        $this->then->_shouldBe('selections/142/user/id', 42);
        $this->then->_shouldBe('selections/142/user/name', "Trick");
        $this->then->_shouldBe('selections/142/dish', 2);
    }

    function _testDefaultDate() {
        $this->given->anOrder_With_MenusEach_Dishes('Test', 1, 1);
        $this->given->dish_OfMenu_Is(1, 1, 'Something');
        $this->given->nowIs('2000-01-03');

        $this->when->iRequestTheSelectionsForTheDefaultDateWithTheToke('token');

        $this->then->_shouldBe('menu/dishes/1/en', 'Something');
    }

    function _testNoSelection() {
        $this->given->anOrder_With_MenusEach_Dishes('Test', 1, 1);
        $this->given->dish_OfMenu_Is(1, 1, 'Something');

        $this->given->theUser('Tick');
        $this->given->theUser('Trick');

        $this->given->_SelectedDish_ForMenu('Tick', 1, 1);

        $this->when->iRequestTheSelectionsFor_WithTheToken('2000-01-03', 'token');

        $this->then->_shouldHaveTheSize('selections', 1);
    }

    function _testAvatars() {
        $this->given->anOrder_With_MenusEach_Dishes('Test', 1, 1);
        $this->given->dish_OfMenu_Is(1, 1, 'Something');

        $this->given->theUser('Anna');
        $this->given->theUser('Bert');

        $this->given->_SelectedDish_ForMenu('Anna', 1, 1);
        $this->given->_SelectedDish_ForMenu('Bert', 1, 1);

        $this->given->_HasAnAvatar('Bert');

        $this->when->iRequestTheSelectionsFor_WithTheToken('2000-01-03', 'token');

        $this->then->_shouldBe('selections/141/user/avatar', 'http://lacarte/user/avatars/default.png');
        $this->then->_shouldBe('selections/142/user/avatar', 'http://lacarte/user/avatars/42.jpg');
    }

}