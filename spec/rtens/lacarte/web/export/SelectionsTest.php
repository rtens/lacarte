<?php
namespace spec\rtens\lacarte\web\export;
 
use rtens\lacarte\web\export\SelectionsComponent;
use spec\rtens\lacarte\Test;
use spec\rtens\lacarte\web\ComponentTest_When;
use spec\rtens\lacarte\web\order\OrderTest;
use watoki\curir\Path;

/**
 * @property SelectionsTest_When when
 */
class SelectionsTest extends OrderTest {

    public function setUp() {
        parent::setUp();
        $this->config->__mock()->method('getApiToken')->willReturn('token');
    }

    function testNoMenu() {
        $this->when->iRequestTheSelectionsFor_WithTheToken('2013-01-04', 'token');

        $this->then->_shouldHaveTheSize('menu', 0);
        $this->then->_shouldHaveTheSize('selections', 0);
        $this->then->_shouldBe('error', 'No menu found for given date.');
    }

    function testWrongDateFormat() {
        $this->when->iRequestTheSelectionsFor_WithTheToken('not a date', 'token');
        $this->then->_shouldBe('error', 'Could not parse date.');
    }

    function testWrongToken() {
        $this->when->iRequestTheSelectionsFor_WithTheToken('2013-01-04', 'not the token');
        $this->then->_shouldBe('error', 'Wrong token.');
    }

    function testNoSelections() {
        $this->given->anOrder_With_MenusEach_Dishes('Test', 1, 3);
        $this->given->dish_OfMenu_Is(1, 1, 'German Text/English Text');
        $this->given->dish_OfMenu_Is(2, 1, 'Only english Text');
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

    function testAllSelections() {
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

        $this->then->_shouldHaveTheSize('selections', 3);
        $this->then->_shouldBe('selections/141/dish', 1);
        $this->then->_shouldBe('selections/142/dish', 2);
        $this->then->_shouldBe('selections/143/dish', 0);

        $this->then->_shouldBe('selections/141/user/id', 41);
        $this->then->_shouldBe('selections/141/user/name', "Tick");
    }

    function testDefaultDate() {
        $this->given->anOrder_With_MenusEach_Dishes('Test', 1, 1);
        $this->given->dish_OfMenu_Is(1, 1, 'Something');
        $this->given->nowIs('2000-01-03');

        $this->when->iRequestTheSelectionsForTheDefaultDateWithTheToke('token');

        $this->then->_shouldBe('menu/dishes/1/en', 'Something');
    }

    function testNoSelection() {
        $this->given->anOrder_With_MenusEach_Dishes('Test', 1, 1);
        $this->given->dish_OfMenu_Is(1, 1, 'Something');

        $this->given->theUser('Tick');
        $this->given->theUser('Trick');

        $this->given->_SelectedDish_ForMenu('Tick', 1, 1);

        $this->when->iRequestTheSelectionsFor_WithTheToken('2000-01-03', 'token');

        $this->then->_shouldHaveTheSize('selections', 1);
    }

}

/**
 * @property SelectionsTest test
 * @property SelectionsComponent component
 */
class SelectionsTest_When extends ComponentTest_When {

    function __construct(Test $test) {
        parent::__construct($test);

        $this->component = $this->test->mf->createTestUnit(SelectionsComponent::$CLASS, array(
            'factory' => $this->test->factory,
            'route' => new Path(),
            'config' => $this->test->config,
            'time' => $this->test->given->time,
            'orderInteractor' => $this->test->given->orderInteractor,
            'userInteractor' => $this->test->given->userInteractor
        ));
    }

    public function iRequestTheSelectionsFor_WithTheToken($date, $token) {
        $this->model = $this->component->doGet($token, $date);
    }

    public function iRequestTheSelectionsForTheDefaultDateWithTheToke($token) {
        $this->iRequestTheSelectionsFor_WithTheToken(null, $token);
    }
}
