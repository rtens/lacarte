<?php
namespace spec\rtens\lacarte\fixtures\resource\order;

use rtens\lacarte\web\order\SelectionResource;
use spec\rtens\lacarte\fixtures\resource\ResourceFixture;
use spec\rtens\lacarte\fixtures\model\OrderFixture;

/**
 * @property SelectionResource component
 * @property OrderFixture order <-
 */
class SelectionFixture extends ResourceFixture {

    public static $CLASS = __CLASS__;

    public function whenIOpenThePageForOrder($orderName) {
        $this->responder = $this->component->doGet($this->order->getOrder($orderName)->id);
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

    public function thenSelection_ShouldBeUnYieldable($int) {
        $int--;
        $this->spec->assertNotNull($this->getField("order/selection/$int/action/unyield"));
        $this->spec->assertNull($this->getField("order/selection/$int/action/yield"));
    }

    public function thenSelection_ShouldBeYieldable($int) {
        $int--;
        $this->spec->assertNull($this->getField("order/selection/$int/action/unyield"));
        $this->spec->assertNotNull($this->getField("order/selection/$int/action/yield"));
    }

    public function thenSelection_ShouldNotBeYieldableNorUnYieldable($int) {
        $int--;
        $this->spec->assertNull($this->getField("order/selection/$int/action/unyield"));
        $this->spec->assertNull($this->getField("order/selection/$int/action/yield"));
    }

    public function thenNotSelectedDish_OfSelection_ShouldBe($int, $int1, $string) {
        $int--;
        $int1--;
        $this->spec->assertEquals($string, $this->getField("order/selection/$int1/notSelected/$int"));
    }

    protected function getComponentClass() {
        return SelectionResource::$CLASS;
    }
}