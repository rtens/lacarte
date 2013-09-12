<?php
namespace spec\rtens\lacarte\fixtures\component\export;

use rtens\lacarte\web\export\DishesComponent;
use rtens\lacarte\web\LaCarteModule;
use spec\rtens\lacarte\fixtures\component\ComponentFixture;
use spec\rtens\lacarte\fixtures\model\OrderFixture;
use spec\rtens\lacarte\fixtures\model\UserFixture;
use spec\rtens\lacarte\fixtures\service\SessionFixture;
use spec\rtens\lacarte\TestCase;
use watoki\factory\Factory;

/**
 * @property DishesComponent $component
 */
class DishesComponentFixture extends ComponentFixture {

    public static $CLASS = __CLASS__;

    /** @var OrderFixture */
    private $order;

    public function __construct(TestCase $test, Factory $factory, LaCarteModule $root, SessionFixture $session) {
        parent::__construct($test, $factory, $root);
        $this->order = $test->useFixture(OrderFixture::$CLASS);
    }

    public function whenIExportTheOrder($string) {
        $this->model = $this->component->doGet($this->order->getOrder($string)->id);
    }

    public function thenThereShouldBe_Rows($int) {
        $this->test->assertCount($int, $this->getField('content'));
    }

    public function thenTheDateOfRow_ShouldBe($int, $string) {
        $this->test->assertEquals($string, $this->getRowField($int, 'date'));
    }

    public function thenTheDishOfRow_ShouldBe($int, $string) {
        $this->test->assertEquals($string, $this->getRowField($int, 'dish'));
    }

    public function thenTheSumOfRow_ShouldBe($int, $sum) {
        $this->test->assertEquals($sum, $this->getRowField($int, 'sum'));
    }

    public function thenTheChoosersOfRow_ShouldBe($int, $string) {
        $this->test->assertEquals($string, $this->getRowField($int, 'by'));
    }

    protected function getComponentClass() {
        return DishesComponent::$CLASS;
    }

    private function getRowField($int, $field) {
        $int--;
        return $this->getField("content/$int/$field");
    }
}