<?php
namespace spec\rtens\lacarte\fixture\component\order;

use rtens\lacarte\web\order\ListComponent;
use spec\rtens\lacarte\fixture\component\ComponentFixture;

/**
 * @property ListComponent $component
 * @property ListComponent $component
 */
class ListComponentFixture extends ComponentFixture {

    public static $CLASS = __CLASS__;

    public function whenIOpenThePage() {
        $this->model = $this->component->doGet();
    }

    public function thenThereShouldBe_OrdersListed($int) {
        $this->test->assertCount($int, $this->getField('order'));
    }

    public function thenTheNameOfOrder_ShouldBe($int, $string) {
        $this->then_OfOrder_ShouldBe('name', $int, $string);
    }

    public function thenTheDeadlineOfOrder_ShouldBe($int, $string) {
        $this->then_OfOrder_ShouldBe('deadline', $int, $string);
    }

    public function thenTheSelectLinkOfOrder_ShouldBe($int, $string) {
        $this->then_OfOrder_ShouldBe('selectLink/href', $int, $string);
    }

    public function thenTheEditLinkOfOrder_ShouldBe($int, $string) {
        $this->then_OfOrder_ShouldBe('editLink/href', $int, $string);
    }

    public function thenTheItemLinkOfOrder_ShouldBe($int, $string) {
        $this->then_OfOrder_ShouldBe('itemLink/href', $int, $string);
    }

    public function thenOrder_ShouldBeOpen($int) {
        $this->then_OfOrder_ShouldBe('isOpen', $int, true);
    }

    public function thenOrder_ShouldNotBeOpen($int) {
        $this->then_OfOrder_ShouldBe('isOpen', $int, false);
    }

    protected function getComponentClass() {
        return ListComponent::$CLASS;
    }

    private function then_OfOrder_ShouldBe($field, $i, $string) {
        $i--;
        $this->test->assertEquals($string, $this->getField("order/$i/$field"));
    }
}