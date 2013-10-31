<?php
namespace spec\rtens\lacarte\fixtures\component\export;

use rtens\lacarte\web\export\DishesResource;
use spec\rtens\lacarte\fixtures\component\ResourceFixture;
use spec\rtens\lacarte\fixtures\model\OrderFixture;

/**
 * @property DishesResource component
 * @property OrderFixture order <-
 */
class DishesComponentFixture extends ResourceFixture {

    public static $CLASS = __CLASS__;

    public function whenIExportTheOrder($string) {
        $this->responder = $this->component->doGet($this->order->getOrder($string)->id);
    }

    public function thenThereShouldBe_Rows($int) {
        $this->spec->assertCount($int, $this->getField('content'));
    }

    public function thenTheDateOfRow_ShouldBe($int, $string) {
        $this->spec->assertEquals($string, $this->getRowField($int, 'date'));
    }

    public function thenTheDishOfRow_ShouldBe($int, $string) {
        $this->spec->assertEquals($string, $this->getRowField($int, 'dish'));
    }

    public function thenTheSumOfRow_ShouldBe($int, $sum) {
        $this->spec->assertEquals($sum, $this->getRowField($int, 'sum'));
    }

    public function thenTheChoosersOfRow_ShouldBe($int, $string) {
        $this->spec->assertEquals($string, $this->getRowField($int, 'by'));
    }

    protected function getComponentClass() {
        return DishesResource::$CLASS;
    }

    private function getRowField($int, $field) {
        $int--;
        return $this->getField("content/$int/$field");
    }
}