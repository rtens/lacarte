<?php
namespace spec\rtens\lacarte\fixtures\resource\order;

use rtens\lacarte\model\Order;
use rtens\lacarte\web\order\SelectResource;
use spec\rtens\lacarte\fixtures\model\OrderFixture;
use spec\rtens\lacarte\fixtures\model\UserFixture;
use spec\rtens\lacarte\fixtures\resource\ResourceFixture;

/**
 * @property SelectResource component
 * @property UserFixture user <-
 * @property OrderFixture order <-
 */
class SelectResourceFixture extends ResourceFixture {

    public static $CLASS = __CLASS__;

    /** @var array */
    private $selections = array();

    /** @var null|Order */
    private $currentOrder;

    public function whenIOpenThePageForOrder($string) {
        $this->responder = $this->component->doGet($this->order->getOrder($string)->id);
    }

    public function whenIOpenThePageForOrderForTheUser($orderName, $userName) {
        $this->responder = $this->component->doGet($this->order->getOrder($orderName)->id, $this->user->getUser($userName));
    }

    public function thenTheDisplayedTimeLeftShouldBe($string) {
        $this->spec->assertEquals($string, $this->getField('order/timeLeft'));
    }

    public function thenTheSelectionOf_ShouldBeLoaded($string) {
        $this->spec->assertEquals($this->user->getUser($string)->id, $this->getField('userId/value'));
    }

    public function thenThereShouldBeNoErrorMessage() {
        $this->thenTheErrorMessageShouldBe(null);
    }

    public function thenThereShouldBeNoSuccessMessage() {
        $this->thenTheSuccessMessageShouldBe(null);
    }

    public function thenTheOrder_ShouldBeLoaded($string) {
        $this->spec->assertEquals($this->order->getOrder($string)->id, $this->getField('order/id/value'));
    }

    public function thenThereShouldBe_Menus($int) {
        $this->spec->assertCount($int, $this->getField('order/menu'));
    }

    public function thenTheDateOfMenu_ShouldBe($int, $string) {
        $this->then_OfMenu_ShouldBe('date', $int, $string);
    }

    public function thenTheNoneOptionOfMenu_ShouldNotBeChecked($int) {
        $this->then_OfMenu_ShouldBe('none/key/checked', $int, false);
    }

    public function thenTheNoneOptionOfMenu_ShouldBeChecked($int) {
        $this->then_OfMenu_ShouldBe('none/key/checked', $int, "checked");
    }

    public function thenMenu_ShouldHave_Dishes($menuNum, $dishCount) {
        $this->spec->assertCount($dishCount, $this->getMenuField('dish', $menuNum));
    }

    public function thenDish_OfMenu_ShouldBe($dishNum, $menuNum, $text) {
        $this->then_OfDish_OfMenu_ShouldBe('text', $dishNum, $menuNum, $text);
    }

    public function thenDish_OfMenu_ShouldNotBeChecked($dishNum, $menuNum) {
        $this->then_OfDish_OfMenu_ShouldBe('key/checked', $dishNum, $menuNum, false);
    }

    public function thenDish_OfMenu_ShouldBeChecked($dishNum, $menuNum) {
        $this->then_OfDish_OfMenu_ShouldBe('key/checked', $dishNum, $menuNum, "checked");
    }

    public function givenIHaveOpenedThePageForOrder($orderName) {
        $this->whenIOpenThePageForOrder($orderName);
        $this->currentOrder = $this->order->getOrder($orderName);
    }

    public function givenISelectedDish_OfMenu($dishText, $menuNum) {
        foreach ($this->getMenuField('dish', $menuNum) as $dish) {
            if ($this->getFieldIn('text', $dish) == $dishText) {
                $matches = array();
                preg_match('/selection\[(\d+)\]/', $this->getFieldIn('key/name', $dish), $matches);
                $this->selections[$matches[1]] = $this->getFieldIn('key/value', $dish);
            }
        }
    }

    public function givenISelectedNoDishOfMenu($int) {
        $dish = $this->getMenuField('none', $int);
        $matches = array();
        preg_match('/selection\[(\d+)\]/', $this->getFieldIn('key/name', $dish), $matches);
        $this->selections[$matches[1]] = $this->getFieldIn('key/value', $dish);
    }

    public function whenISaveMySelections() {
        $this->responder = $this->component->doPost($this->currentOrder->id, $this->selections);
    }

    protected function getComponentClass() {
        return SelectResource::$CLASS;
    }

    public function thenTheErrorMessageShouldBe($value) {
        $this->spec->assertEquals($value, $this->getField('error'));
    }

    public function thenTheSuccessMessageShouldBe($value) {
        $this->spec->assertEquals($value, $this->getField('success'));
    }

    private function then_OfMenu_ShouldBe($field, $menuNum, $value) {
        $this->spec->assertEquals($value, $this->getMenuField($field, $menuNum));
    }

    private function getMenuField($field, $menuNum) {
        $menuNum--;
        return $this->getField("order/menu/$menuNum/$field");
    }

    private function then_OfDish_OfMenu_ShouldBe($field, $dishNum, $menuNum, $value) {
        $dishNum--;
        $this->then_OfMenu_ShouldBe("dish/$dishNum/$field", $menuNum, $value);
    }
}