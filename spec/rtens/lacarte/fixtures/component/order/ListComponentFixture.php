<?php
namespace spec\rtens\lacarte\fixtures\component\order;

use rtens\lacarte\web\order\ListComponent;
use spec\rtens\lacarte\fixtures\component\ComponentFixture;

/**
 * @property ListComponent component
 */
class ListComponentFixture extends ComponentFixture {

    public static $CLASS = __CLASS__;

    private $firstDay;

    private $lastDay;

    private $deadline;

    public function whenIOpenThePage() {
        $this->model = $this->component->doGet();
    }

    public function thenThereShouldBe_OrdersListed($int) {
        $this->spec->assertCount($int, $this->getField('order'));
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

    public function thenTheFirstDayFieldShouldContain($string) {
        $this->then_ShouldBe('firstDay/value', $string);
    }

    public function thenTheLastDayFieldShouldContain($string) {
        $this->then_ShouldBe('lastDay/value', $string);
    }

    public function thenTheDeadlineFieldShouldContain($string) {
        $this->then_ShouldBe('deadline/value', $string);
    }

    public function thenThereShouldBeNoErrorMessage() {
        $this->then_ShouldBe('error', null);
    }

    public function givenIHaveEnteredTheFirstDay($string) {
        $this->firstDay = $string;
    }

    public function givenIHaveEnteredTheLastDay($string) {
        $this->lastDay = $string;
    }

    public function givenIHaveEnteredTheDeadline($string) {
        $this->deadline = $string;
    }

    public function whenICreateANewOrder() {
        $this->model = $this->component->doPost($this->firstDay, $this->lastDay, $this->deadline);
    }

    public function thenTheErrorMessageShouldBe($string) {
        $this->spec->assertEquals($string, $this->getField('error'));
    }

    public function thenTheErrorMessageShouldContain($string) {
        $this->spec->assertContains($string, $this->getField('error'));
    }

    public function thenItShouldDisplayTodaysOrder($string) {
        $this->spec->assertEquals($string, $this->getField('today/dish'));
    }

    public function thenThereShouldBeNoTodaysOrder() {
        $this->spec->assertEquals(null, $this->getField('today'));
    }

    protected function getComponentClass() {
        return ListComponent::$CLASS;
    }

    private function then_OfOrder_ShouldBe($field, $i, $string) {
        $i--;
        $this->then_ShouldBe("order/$i/$field", $string);
    }

    private function then_ShouldBe($field, $value) {
        $this->spec->assertEquals($value, $this->getField($field));
    }
}