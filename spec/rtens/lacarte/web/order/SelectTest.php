<?php
namespace spec\rtens\lacarte\web\order;

use Symfony\Component\Console\Tests\Tester\CommandTesterTest;
use rtens\lacarte\core\NotFoundException;
use rtens\lacarte\model\Selection;
use rtens\lacarte\web\order\SelectComponent;
use spec\rtens\lacarte\Test;
use spec\rtens\lacarte\web\ComponentTest_Then;
use spec\rtens\lacarte\web\ComponentTest_When;
use watoki\collections\Map;
use watoki\collections\Set;

/**
 * @property SelectTest_Given given
 * @property SelectTest_When when
 * @property SelectTest_Then then
 */
class SelectTest extends OrderTest {

    function testAfterDeadline() {
        $this->given->anOrder_With_MenusEach_Dishes('too late', 2, 2);
        $this->given->theDeadlineOfTheOrderIs('2000-01-01 00:00');
        $this->given->nowIs('2000-01-01 00:01');

        $this->when->iAccessThePageForTheOrder();

        $this->then->iShouldBeRedirectedTo('selection.html?order=12');
    }

    function testAdminAfterDeadline() {
        $this->given->iAmLoggedInAsAdmin();
        $this->given->anOrder_With_MenusEach_Dishes('too late admin', 2, 2);
        $this->given->theDeadlineOfTheOrderIs('2000-01-01 00:00');
        $this->given->nowIs('2000-01-01 01:01');

        $this->when->iAccessThePageForTheOrderForTheUser(123);

        $this->then->_shouldBe('order/timeLeft', '-0d 1h 1m');
        $this->then->_shouldBe('userId/value', 123);
    }

    function testNoSelection() {
        $this->given->iAmLoggedInAsUser();
        $this->given->anOrder_With_MenusEach_Dishes('test', 2, 2);
        $this->given->theDeadlineOfTheOrderIs('2000-01-01 00:00');
        $this->given->nowIs('1999-12-29 21:38');
        $this->given->dish_OfMenu_Is(1, 1, 'A');
        $this->given->dish_OfMenu_Is(2, 1, 'B');
        $this->given->dish_OfMenu_Is(1, 2, 'C');
        $this->given->dish_OfMenu_Is(2, 2, 'D');

        $this->when->iAccessThePageForTheOrder();

        $this->then->_shouldBe('error', null);
        $this->then->_shouldBe('success', null);
        $this->then->_shouldBe('order/id/value', 12);
        $this->then->_shouldBe('order/timeLeft', '2d 2h 22m');
        $this->then->_shouldHaveTheSize('order/menu', 2);
        $this->then->_shouldBe('order/menu/0/date', 'Monday, 3.1.2000');
        $this->then->_shouldBe('order/menu/0/none/key/name', 'selection[1]');
        $this->then->_shouldBe('order/menu/0/none/key/checked', false);
        $this->then->_shouldHaveTheSize('order/menu/0/dish', 2);
        $this->then->_shouldBe('order/menu/0/dish/0/key/name', 'selection[1]');
        $this->then->_shouldBe('order/menu/0/dish/0/key/value', '1');
        $this->then->_shouldBe('order/menu/0/dish/0/text', 'A');

        $this->then->_shouldBe('order/menu/1/date', 'Tuesday, 4.1.2000');
        $this->then->_shouldBe('order/menu/1/none/key/name', 'selection[2]');
    }

    function testCreateSelection() {
        $this->given->iAmLoggedInAsUser();
        $this->given->anOrder_With_MenusEach_Dishes('test', 3, 2);

        $this->given->iHaveSelectedDish_OfMenu(1, 1);
        $this->given->iHaveSelectedDish_OfMenu(4, 2);
        $this->given->iHaveSelectedDish_OfMenu(0, 3);

        $this->when->iSaveTheSelection();

        $this->then->_SelectionsShouldBeSaved(3);
        $this->then->theUserOfSelection_ShouldBeMe(1);
        $this->then->theMenuOfSelection_ShouldBe(1, 1);
        $this->then->theDishOfSelection_ShouldBe(1, 1);
        $this->then->theDishOfSelection_ShouldBe(2, 4);
        $this->then->theSelection_ShouldHaveNoDish(3);

        $this->then->_shouldBe('success', 'Selection saved');
    }

    function testLoadSelections() {
        $this->given->iAmLoggedInAsUser();
        $this->given->anOrder_With_MenusEach_Dishes('test', 3, 2);
        $this->given->theDeadlineOfTheOrderIs('2000-01-01 00:00');
        $this->given->nowIs('1999-12-29 21:38');
        $this->given->theMenu_HasASelectionForNoDish_ByMe(1);
        $this->given->theMenu_HasASelectionForDish_ByMe(2, 3);

        $this->when->iAccessThePageForTheOrder();

        $this->then->_shouldHaveTheSize('order/menu', 3);
        $this->then->_shouldHaveTheSize('order/menu/0/dish', 2);
        $this->then->_shouldBe('order/menu/0/none/key/name', 'selection[1]');
        $this->then->_shouldBe('order/menu/0/none/key/checked', "checked");
        $this->then->_shouldBe('order/menu/0/dish/0/key/checked', false);
        $this->then->_shouldBe('order/menu/0/dish/1/key/checked', false);

        $this->then->_shouldHaveTheSize('order/menu/1/dish', 2);
        $this->then->_shouldBe('order/menu/1/none/key/name', 'selection[2]');
        $this->then->_shouldBe('order/menu/1/none/key/checked', false);
        $this->then->_shouldBe('order/menu/1/dish/0/key/checked', "checked");
        $this->then->_shouldBe('order/menu/1/dish/1/key/checked', false);

        $this->then->_shouldHaveTheSize('order/menu/2/dish', 2);
        $this->then->_shouldBe('order/menu/2/none/key/name', 'selection[3]');
        $this->then->_shouldBe('order/menu/2/none/key/checked', false);
        $this->then->_shouldBe('order/menu/2/dish/0/key/checked', false);
        $this->then->_shouldBe('order/menu/2/dish/1/key/checked', false);
    }

    function testUpdateSelections() {
        $this->given->iAmLoggedInAsUser();
        $this->given->anOrder_With_MenusEach_Dishes('test', 3, 2);
        $this->given->theDeadlineOfTheOrderIs('2000-01-01 00:00');
        $this->given->nowIs('1999-12-29 21:38');
        $this->given->theMenu_HasASelectionForDish_ByMe(1, 2);
        $this->given->theMenu_HasASelectionForDish_ByMe(2, 3);
        $this->given->theMenu_HasASelectionForNoDish_ByMe(3);

        $this->given->iHaveSelectedDish_OfMenu(0, 1);
        $this->given->iHaveSelectedDish_OfMenu(3, 2);
        $this->given->iHaveSelectedDish_OfMenu(5, 3);

        $this->when->iSaveTheSelection();

        $this->then->_SelectionsShouldBeSaved(3);
        $this->then->theUserOfSelection_ShouldBeMe(1);
        $this->then->theIdOfSelection_ShouldBe(1, 1);
        $this->then->theIdOfSelection_ShouldBe(2, 2);
        $this->then->theIdOfSelection_ShouldBe(3, 3);
    }

    function testNotAllSelected() {
        $this->given->iAmLoggedInAsUser();
        $this->given->anOrder_With_MenusEach_Dishes('test', 3, 2);

        $this->given->iHaveSelectedDish_OfMenu(1, 1);
        $this->given->iHaveSelectedDish_OfMenu(0, 3);

        $this->when->iSaveTheSelection();

        $this->then->NoSelectionsShouldBeSaved();

        $this->then->_shouldBe('error', 'Please make a selection for every day');

        $this->then->_shouldHaveTheSize('order/menu', 3);
        $this->then->_shouldHaveTheSize('order/menu/0/dish', 2);
        $this->then->_shouldBe('order/menu/0/none/key/name', 'selection[1]');
        $this->then->_shouldBe('order/menu/0/none/key/checked', false);
        $this->then->_shouldBe('order/menu/0/dish/0/key/checked', "checked");
        $this->then->_shouldBe('order/menu/0/dish/1/key/checked', false);

        $this->then->_shouldHaveTheSize('order/menu/1/dish', 2);
        $this->then->_shouldBe('order/menu/1/none/key/name', 'selection[2]');
        $this->then->_shouldBe('order/menu/1/none/key/checked', false);
        $this->then->_shouldBe('order/menu/1/dish/0/key/checked', false);
        $this->then->_shouldBe('order/menu/1/dish/1/key/checked', false);

        $this->then->_shouldHaveTheSize('order/menu/2/dish', 2);
        $this->then->_shouldBe('order/menu/2/none/key/name', 'selection[3]');
        $this->then->_shouldBe('order/menu/2/none/key/checked', "checked");
        $this->then->_shouldBe('order/menu/2/dish/0/key/checked', false);
        $this->then->_shouldBe('order/menu/2/dish/1/key/checked', false);
    }

}

/**
 * @property SelectTest test
 */
class SelectTest_Given extends OrderTest_Given {

    /** @var Map */
    public $selected;

    /** @var array|Set[] */
    public $selections = array();

    function __construct(Test $test) {
        parent::__construct($test);
        $this->selected = new Map();
        $this->orderInteractor->__mock()->method('readSelectionByMenuIdAndUserId')->willThrow(new NotFoundException());
    }

    public function iHaveSelectedDish_OfMenu($dishId, $menuId) {
        $this->selected[$menuId] = $dishId;
    }

    public function theMenu_HasASelectionForNoDish_ByMe($menuId) {
        $this->theMenu_HasASelectionForDish_ByMe($menuId, 0);
    }

    public function theMenu_HasASelectionForDish_ByMe($menuId, $dishId) {
        $this->selections[$menuId] = new Selection($this->me->id, $menuId, $dishId);
        $this->selections[$menuId]->id = count($this->selections);
        $this->orderInteractor->__mock()->method('readSelectionByMenuIdAndUserId')
            ->willReturn($this->selections[$menuId])->withArguments($menuId, $this->me->id);
    }
}

/**
 * @property SelectTest test
 * @property SelectComponent component
 */
class SelectTest_When extends ComponentTest_When {

    function __construct(Test $test) {
        parent::__construct($test);
        $this->createDefaultComponent(SelectComponent::$CLASS, array(
            'orderInteractor' => $this->test->given->orderInteractor,
            'time' => $this->test->given->time
        ));
    }

    public function iAccessThePageForTheOrder() {
        $this->model = $this->component->doGet($this->test->given->order->id);
    }

    public function iSaveTheSelection() {
        $this->model = $this->component->doPost($this->test->given->order->id, $this->test->given->selected);
    }

    public function iAccessThePageForTheOrderForTheUser($userId) {
        $this->model = $this->component->doGet($this->test->given->order->id, $userId);
    }
}

/**
 * @property SelectTest test
 */
class SelectTest_Then extends ComponentTest_Then {

    function __construct(Test $test) {
        parent::__construct($test);
    }

    public function thereShouldBe_Selections($count) {
        $this->test->markTestIncomplete();
    }

    public function theUserOfSelection_ShouldBeMe($index) {
        $selections = $this->getSelections();
        $this->test->assertEquals($this->test->given->me->id, $selections[$index - 1]->getUserId());
    }

    public function theMenuOfSelection_ShouldBe($index, $menuId) {
        $selections = $this->getSelections();
        $this->test->assertEquals($menuId, $selections[$index - 1]->getMenuId());
    }

    public function theDishOfSelection_ShouldBe($index, $dishId) {
        $selections = $this->getSelections();
        $this->test->assertEquals($dishId, $selections[$index - 1]->getDishId());
    }

    public function theSelection_ShouldHaveNoDish($index) {
        $selections = $this->getSelections();
        $this->test->assertFalse($selections[$index - 1]->hasDish());
    }

    public function _SelectionsShouldBeSaved($count) {
        $this->test->assertTrue($this->getMethod()->wasCalled());
        $this->test->assertEquals($count, count($this->getMethod()->getCalledArgumentAt(0, 0)));
    }

    private function getMethod() {
        return $this->test->given->orderInteractor->__mock()->method('saveSelections');
    }

    /**
     * @return Selection[]
     */
    private function getSelections() {
        return $this->getMethod()->getCalledArgumentAt(0, 0);
    }

    public function theIdOfSelection_ShouldBe($index, $id) {
        $selections = $this->getSelections();
        $this->test->assertEquals($id, $selections[$index - 1]->id);
    }

    public function NoSelectionsShouldBeSaved() {
        $this->test->assertFalse($this->getMethod()->wasCalled());
    }
}