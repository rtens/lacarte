<?php
namespace spec\rtens\lacarte\fixtures\component\order;

use rtens\lacarte\model\Order;
use rtens\lacarte\model\stores\DishStore;
use rtens\lacarte\model\stores\MenuStore;
use rtens\lacarte\web\order\EditComponent;
use spec\rtens\lacarte\fixtures\component\ComponentFixture;
use spec\rtens\lacarte\fixtures\model\OrderFixture;

/**
 * @property EditComponent component
 * @property OrderFixture order<-
 * @property MenuStore menuStore<-
 * @property DishStore dishStore<-
 */
class EditComponentFixture extends ComponentFixture {

    public static $CLASS = __CLASS__;

    /** @var array|string[] */
    private $dishTexts = array();

    /** @var null|Order */
    private $currentOrder;

    public function whenIOpenThePageToEdit($orderName) {
        $this->model = $this->component->doGet($this->order->getOrder($orderName)->id);
    }

    public function whenISaveTheOrder() {
        $this->model = $this->component->doPost($this->currentOrder->id, $this->dishTexts);
    }

    public function thenThereShouldBeNoErrorMessage() {
        $this->thenTheErrorMessageShouldBe(null);
    }

    public function thenTheErrorMessageShouldBe($string) {
        $this->spec->assertEquals($string, $this->getField('error'));
    }

    public function thenThereShouldBeNoSuccessMessage() {
        $this->thenTheSuccessMessageShouldBe(null);
    }

    public function thenTheSuccessMessageShouldBe($string) {
        $this->spec->assertEquals($string, $this->getField('success'));
    }

    public function thenTheNameOfTheOrderShouldBe($string) {
        $this->spec->assertEquals($string, $this->getField('order/name'));
        $this->spec->assertEquals($this->order->getOrder($string)->id, $this->getField('order/id/value'));
    }

    public function thenThereShouldBe_Menus($int) {
        $this->spec->assertCount($int, $this->getField('order/menu'));
    }

    public function thenTheDateOfMenu_ShouldBe($num, $string) {
        $num--;
        $this->spec->assertEquals($string, $this->getField("order/menu/$num/date"));
    }

    public function thenMenu_ShouldHave_Dishes($menuNum, $countDishes) {
        $menuNum--;
        $this->spec->assertCount($countDishes, $this->getField("order/menu/$menuNum/dish"));
    }

    public function thenDish_OfMenu_ShouldBe($dishNum, $menuNum, $text) {
        $menuNum--;
        $dishNum--;
        $id = $this->order->getDish($text)->id;
        $this->spec->assertEquals($text, $this->getField("order/menu/$menuNum/dish/$dishNum/text/_"));
        $this->spec->assertEquals("dish[$id]", $this->getField("order/menu/$menuNum/dish/$dishNum/text/name"));
    }

    public function givenIHaveEntered_ForDish_OfMenu($text, $dishNum, $menuNum) {
        $menus = $this->menuStore->readAllByOrderId($this->currentOrder->id);
        $dishes = $this->dishStore->readAllByMenuId($menus[$menuNum - 1]->id);
        $this->dishTexts[$dishes[$dishNum - 1]->id] = $text;
    }

    public function givenIHaveOpenedThePageToEdit($orderName) {
        $this->currentOrder = $this->order->getOrder($orderName);
    }

    protected function getComponentClass() {
        return EditComponent::$CLASS;
    }
}