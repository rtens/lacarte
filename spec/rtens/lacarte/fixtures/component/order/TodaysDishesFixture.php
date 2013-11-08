<?php


namespace spec\rtens\lacarte\fixtures\component\order;


use rtens\lacarte\web\order\TodaysDishesComponent;
use spec\rtens\lacarte\fixtures\component\ComponentFixture;


/**
 * @property TodaysDishesComponent component
 */
class TodaysDishesFixture extends ComponentFixture {

    public static $CLASS = __CLASS__;

    public function whenIOpenThePage() {
        $this->model = $this->component->doGet();
    }


    public function thenThereShouldBe_Dishes($int) {
        $this->spec->assertCount($int, $this->getField('dish'));
    }


    protected function getComponentClass()
    {
        return TodaysDishesComponent::$CLASS;
    }
}