<?php
namespace spec\rtens\lacarte\web\order;
 
use rtens\lacarte\OrderInteractor;
use rtens\lacarte\model\Dish;
use rtens\lacarte\model\Menu;
use rtens\lacarte\model\Order;
use rtens\lacarte\web\order\EditComponent;
use rtens\mockster\Mock;
use spec\rtens\lacarte\Test;
use spec\rtens\lacarte\web\ComponentTest;
use spec\rtens\lacarte\web\ComponentTest_Given;
use spec\rtens\lacarte\web\ComponentTest_Then;
use spec\rtens\lacarte\web\ComponentTest_When;
use watoki\collections\Collection;
use watoki\collections\Map;
use watoki\collections\Set;
use watoki\curir\Path;

/**
 * @property EditOrderTest_Given given
 * @property EditOrderTest_When when
 * @property EditOrderTest_Then then
 */
class EditOrderTest extends OrderTest {

    function testNotAdminOnAccess() {
        $this->given->anOrder_With_MenusEach_Dishes('test', 2, 2);

        $this->when->iAccessThePage();

        $this->then->iShouldBeRedirectedTo('list.html');
    }

    function testNotAdminOnSave() {
        $this->given->anOrder_With_MenusEach_Dishes('test2', 2, 3);

        $this->when->iSaveTheDishes();

        $this->then->iShouldBeRedirectedTo('list.html');
    }

    function testLoadDishes() {
        $this->given->iAmLoggedInAsAdmin();
        $this->given->anOrder_With_MenusEach_Dishes('test', 2, 2);
        $this->given->dish_OfMenu_Is(1, 1, 'A');
        $this->given->dish_OfMenu_Is(2, 1, 'B');
        $this->given->dish_OfMenu_Is(1, 2, 'C');
        $this->given->dish_OfMenu_Is(2, 2, 'D');

        $this->when->iAccessThePage();

        $this->then->_shouldBe('error', null);
        $this->then->_shouldBe('success', null);

        $this->then->_shouldBe('order/name', 'test');
        $this->then->_shouldBe('order/id/value', 12);

        $this->then->_shouldHaveTheSize('order/menu', 2);
        $this->then->_shouldBe('order/menu/0/date', 'Monday, 3.1.2000');
        $this->then->_shouldBe('order/menu/1/date', 'Tuesday, 4.1.2000');
        $this->then->_shouldHaveTheSize('order/menu/0/dish', 2);
        $this->then->_shouldBe('order/menu/0/dish/0/text/_', 'A');
        $this->then->_shouldBe('order/menu/0/dish/1/text/_', 'B');
        $this->then->_shouldBe('order/menu/1/dish/0/text/_', 'C');
        $this->then->_shouldBe('order/menu/1/dish/1/text/_', 'D');

        $this->then->_shouldBe('order/menu/0/dish/0/text/name', 'dish[1]');
        $this->then->_shouldBe('order/menu/0/dish/1/text/name', 'dish[2]');
        $this->then->_shouldBe('order/menu/1/dish/0/text/name', 'dish[3]');
        $this->then->_shouldBe('order/menu/1/dish/1/text/name', 'dish[4]');
    }

    function testSaveDishes() {
        $this->given->iAmLoggedInAsAdmin();
        $this->given->anOrder_With_MenusEach_Dishes('test2', 2, 3);
        $this->given->iHaveEntered_ForDish_OfMenu('W', 1, 1);
        $this->given->iHaveEntered_ForDish_OfMenu('X', 2, 1);
        $this->given->iHaveEntered_ForDish_OfMenu('Y', 1, 2);
        $this->given->iHaveEntered_ForDish_OfMenu('Z', 3, 2);

        $this->when->iSaveTheDishes();

        $this->then->_DishesShouldBeSaved(6);

        $this->then->_shouldBe('success', 'Order saved');
        $this->then->_shouldBe('error', null);
        $this->then->_shouldBe('order/name', 'test2');
    }

    function testError() {
        $this->given->iAmLoggedInAsAdmin();
        $this->given->anOrder_With_MenusEach_Dishes('error', 1, 1);
        $this->given->anErrorOccurs('Something went wrong');

        $this->when->iSaveTheDishes();

        $this->then->_shouldBe('error', 'Something went wrong');
    }

}

class EditOrderTest_Given extends OrderTest_Given {

    public function iHaveEntered_ForDish_OfMenu($text, $dishNum, $menuNum) {
        $this->dish_OfMenu_Is($dishNum, $menuNum, $text);
    }

    public function anErrorOccurs($string) {
        $this->orderInteractor->__mock()->method('updateDishes')->willThrow(new \Exception($string));
    }
}

/**
 * @property EditOrderTest test
 * @property EditComponent component
 */
class EditOrderTest_When extends ComponentTest_When {

    function __construct(Test $test) {
        parent::__construct($test);
        $this->createDefaultComponent(EditComponent::$CLASS, array(
            'orderInteractor' => $this->test->given->orderInteractor
        ));
    }

    public function iAccessThePage() {
        $this->model = $this->component->doGet($this->test->given->order->id);
    }

    public function iSaveTheDishes() {
        $this->model = $this->component->doPost($this->test->given->order->id, $this->test->given->dishes);
    }
}

/**
 * @property EditOrderTest test
 */
class EditOrderTest_Then extends ComponentTest_Then {

    public function _DishesShouldBeSaved($count) {
        $method = $this->test->given->orderInteractor->__mock()->method('updateDishes');
        $this->test->assertTrue($method->wasCalled(), 'updateDishes was not called');

        /** @var Collection $dishes */
        $dishes = $method->getCalledArgumentAt(0, 0);
        $this->test->assertEquals($count, $dishes->count());
    }
}
