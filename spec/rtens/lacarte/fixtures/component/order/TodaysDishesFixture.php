<?php


namespace spec\rtens\lacarte\fixtures\component\order;

use rtens\lacarte\web\order\TodaysDishesResource;
use spec\rtens\lacarte\fixtures\resource\ResourceFixture;


/**
 * @property TodaysDishesResource component
 */
class TodaysDishesFixture extends ResourceFixture {

    public static $CLASS = __CLASS__;

    public function whenIOpenThePage() {
        $this->model = $this->component->doGet();
    }


    public function thenThereShouldBe_Dishes($int) {
        $this->spec->assertCount($int, $this->getField('dish'));
    }


    protected function getComponentClass()
    {
        return TodaysDishesResource::$CLASS;
    }
}