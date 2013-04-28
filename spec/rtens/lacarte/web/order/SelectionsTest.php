<?php
namespace spec\rtens\lacarte\web\order;

use rtens\lacarte\model\Selection;
use rtens\lacarte\model\User;
use rtens\lacarte\web\order\SelectionsComponent;
use spec\rtens\lacarte\Test;
use spec\rtens\lacarte\web\ComponentTest_When;
use watoki\collections\Liste;

/**
 * @property SelectionsTest_Given given
 * @property SelectionsTest_When when
 */
class SelectionsTest extends OrderTest {

    function testNotAdmin() {
        $this->given->anOrder_With_MenusEach_Dishes('My Order', 0, 0);
        $this->when->iAccessThePage();
        $this->then->iShouldBeRedirectedTo('list.html');
    }

    function testNoUsers() {
        $this->given->iAmLoggedInAsAdmin();
        $this->given->anOrder_With_MenusEach_Dishes('Test Order', 3, 2);

        $this->when->iAccessThePage();

        $this->then->_shouldBe('success', null);
        $this->then->_shouldBe('error', null);

        $this->then->_shouldHaveTheSize('actions', 3);
        $this->then->_shouldBe('actions/edit/href', 'edit.html?order=12');
        $this->then->_shouldBe('actions/exportByDish', null);
        $this->then->_shouldBe('actions/exportByUser', null);

        $this->then->_shouldBe('order/name', 'Test Order');
        $this->then->_shouldBe('order/id/value', 12);

        $this->then->_shouldHaveTheSize('order/date', 3);
        $this->then->_shouldBe('order/date/0', 'Mon');

        $this->then->_shouldHaveTheSize('order/user', 0);
    }

    function testNoSelections() {
        $this->given->iAmLoggedInAsAdmin();
        $this->given->anOrder_With_MenusEach_Dishes('Test Order', 3, 2);

        $this->given->theUser('Clark Kent');
        $this->given->theUser('Donald Duck');
        $this->given->theUser('Peter Parker');

        $this->when->iAccessThePage();

        $this->then->_shouldHaveTheSize('order/user', 3);
        $this->then->_shouldBe('order/user/0/name', 'Clark Kent');
        $this->then->_shouldBe('order/user/0/selectLink/href', 'select.html?order=12&user=41');
        $this->then->_shouldHaveTheSize('order/user/0/selection', 3);
        $this->then->_shouldBe('order/user/0/selection/0/selected', false);
        $this->then->_shouldBe('order/user/0/selection/1/selected', false);
        $this->then->_shouldBe('order/user/0/selection/2/selected', false);
    }

    function testUsersWithSelections() {
        $this->given->iAmLoggedInAsAdmin();
        $this->given->anOrder_With_MenusEach_Dishes('Test Order', 1, 2);
        $this->given->dish_OfMenu_Is(1, 1, 'Dish One');
        $this->given->dish_OfMenu_Is(2, 1, 'Dish Two');

        $this->given->theUser('Donald');
        $this->given->theUser('Peter');
        $this->given->theUser('Clark');

        $this->given->_SelectedDish_ForMenu('Donald', 2, 1);
        $this->given->_SelectedDish_ForMenu('Peter', 1, 1);
        $this->given->_SelectedDish_ForMenu('Clark', 0, 1);

        $this->when->iAccessThePage();

        $this->then->_shouldBe('order/user/0/selection/0/selected/title', 'Dish Two');
        $this->then->_shouldBe('order/user/0/selection/0/selected/_', 'Dish...');
        $this->then->_shouldBe('order/user/1/selection/0/selected/title', 'Dish One');
        $this->then->_shouldBe('order/user/1/selection/0/selected/_', 'Dish...');
        $this->then->_shouldBe('order/user/2/selection/0/selected/title', 'nothing');
        $this->then->_shouldBe('order/user/2/selection/0/selected/_', '-');
    }

}

/**
 * @property SelectionsTest test
 */
class SelectionsTest_Given extends OrderTest_Given {

    /** @var array|User[] */
    private $users = array();

    public function theUser($name) {
        $user = new User($this->group->id, $name, $name . '@test.com', $name);
        $user->id = count($this->users) + 41;
        $this->users[$name] = $user;
        $this->userInteractor->__mock()->method('readAllByGroup')->willReturn(new Liste($this->users));
    }

    public function _SelectedDish_ForMenu($user, $dishId, $menuId) {
        $this->orderInteractor->__mock()->method('readSelectionByMenuIdAndUserId')
            ->willReturn(new Selection($this->users[$user]->id, $menuId, $dishId))
            ->withArguments($menuId, $this->users[$user]->id);
    }
}

/**
 * @property SelectionsTest test
 * @property SelectionsComponent component
 */
class SelectionsTest_When extends ComponentTest_When {

    function __construct(Test $test) {
        parent::__construct($test);
        $this->createDefaultComponent(SelectionsComponent::$CLASS, array(
            'orderInteractor' => $this->test->given->orderInteractor
        ));
    }

    public function iAccessThePage() {
        $this->model = $this->component->doGet($this->test->given->order->id);
    }
}