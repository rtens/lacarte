<?php
namespace spec\rtens\lacarte\fixtures\component\order;

use rtens\lacarte\web\order\SelectionComponent;
use spec\rtens\lacarte\fixtures\component\ComponentFixture;
use spec\rtens\lacarte\fixtures\model\OrderFixture;

/**
 * @property SelectionComponent component
 * @property OrderFixture order<-
 */
class SelectionComponentFixture extends ComponentFixture {

    public static $CLASS = __CLASS__;

    public function whenIOpenThePageForOrder($orderName) {
        $this->model = $this->component->doGet($this->order->getOrder($orderName)->id);
    }

    public function thenTheErrorMessageShouldBe($string) {
        $this->spec->assertEquals($string, $this->getField('error'));
    }

    public function thenThereShouldBeNoErrorMessage() {
        $this->thenTheErrorMessageShouldBe(null);
    }

    public function thenTheNameOfTheOrderShouldBe($string) {
        $this->spec->assertEquals($string, $this->getField('order/name'));
    }

    public function thenThereShouldBe_Selections($int) {
        $this->spec->assertCount($int, $this->getField('order/selection'));
    }

    public function thenTheDateOfSelection_ShouldBe($int, $string) {
        $int--;
        $this->spec->assertEquals($string, $this->getField("order/selection/$int/date"));
    }

    public function thenTheSelectedDishOfSelection_ShouldBe($int, $string) {
        $int--;
        $this->spec->assertEquals($string, $this->getField("order/selection/$int/dish"));
    }

    public function thenSelection_ShouldHave_NotSelectedDish($int, $int1) {
        $int--;
        $this->spec->assertCount($int1, $this->getField("order/selection/$int/notSelected"));
    }

    public function thenNotSelectedDish_OfSelection_ShouldBe($int, $int1, $string) {
        $int--;
        $int1--;
        $this->spec->assertEquals($string, $this->getField("order/selection/$int1/notSelected/$int"));
    }

    protected function getComponentClass() {
        return SelectionComponent::$CLASS;
    }
}