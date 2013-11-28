<?php


namespace spec\rtens\lacarte\fixtures\resource\order;

use rtens\lacarte\web\order\TodaysDishesResource;
use spec\rtens\lacarte\fixtures\resource\ResourceFixture;


/**
 * @property TodaysDishesResource component
 */
class TodaysDishesFixture extends ResourceFixture {

    public static $CLASS = __CLASS__;

    public function whenIOpenThePage() {
        $this->responder = $this->component->doGet();
    }


    public function thenThereShouldBe_Dishes($int) {
        $this->spec->assertCount($int, $this->getField('dish'));
    }

    public function thenThereShouldBeNoFood() {
        $this->spec->assertNull($this->getField('dish'));
        $this->spec->assertTrue($this->getField('nothing'));
    }

    public function thenDish_ShouldBe($int, $string) {
        $int--;
        $this->spec->assertFalse($this->getField('nothing'));
        $this->spec->assertEquals($string, $this->getField("dish/$int"));
    }


    protected function getComponentClass() {
        return TodaysDishesResource::$CLASS;
    }
}