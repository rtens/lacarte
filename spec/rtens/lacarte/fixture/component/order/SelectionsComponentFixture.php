<?php
namespace spec\rtens\lacarte\fixture\component\order;

use rtens\lacarte\web\LaCarteModule;
use rtens\lacarte\web\order\SelectionsComponent;
use spec\rtens\lacarte\fixture\component\ComponentFixture;
use spec\rtens\lacarte\fixture\model\OrderFixture;
use spec\rtens\lacarte\fixture\model\SessionFixture;
use spec\rtens\lacarte\fixture\model\UserFixture;
use spec\rtens\lacarte\TestCase;
use watoki\factory\Factory;

/**
 * @property SelectionsComponent $component
 */
class SelectionsComponentFixture extends ComponentFixture {

    public static $CLASS = __CLASS__;

    public function __construct(TestCase $test, Factory $factory, UserFixture $user, LaCarteModule $root,
                                SessionFixture $session, OrderFixture $order) {
        parent::__construct($test, $factory, $user, $root, $session);

        $this->order = $order;
    }

    public function whenIOpenThePageForOrder($name) {
        $this->model = $this->component->doGet($this->order->getOrder($name)->id);
    }

    public function thenThereShouldBeNoSuccessMessage() {
        $this->thenTheSuccessMessageShouldBe(null);
    }

    public function thenThereShouldBeNoErrorMessage() {
        $this->thenTheErrorMessageShouldBe(null);
    }

    public function thenTheEditActionShouldGoTo($string) {
        $this->test->assertEquals($string, $this->getField('actions/edit/href'));
    }

    public function thenTheExportByDishActionShouldGoTo($string) {
        $this->test->assertEquals($string, $this->getField('actions/exportByDish/href'));
    }

    public function thenThereShouldBeNoExportByUserAction() {
        $this->test->assertNull($this->getField('actions/exportByUser'));
    }

    public function thenNameOfTheOrderShouldBe($string) {
        $this->test->assertEquals($string, $this->getField('order/name'));
        $this->test->assertEquals($this->order->getOrder($string)->id, $this->getField('order/id/value'));
    }

    public function thenTheOrderShouldHave_Dates($int) {
        $this->test->assertCount($int, $this->getField('order/date'));
    }

    public function thenDate_ShouldBe($int, $string) {
        $int--;
        $this->test->assertEquals($string, $this->getField("order/date/$int"));
    }

    public function thenThereShouldBe_Users($int) {
        $this->test->assertCount($int, $this->getField('order/user'));
    }

    public function thenTheNameOfUser_ShouldBe($int, $string) {
        $int--;
        $this->test->assertEquals($string, $this->getField("order/user/$int/name"));
    }

    public function thenTheSelectionLinkOfUser_ShouldBe($int, $string) {
        $int--;
        $this->test->assertEquals($string, $this->getField("order/user/$int/selectLink/href"));
    }

    public function thenUser_ShouldHave_Selections($int, $int1) {
        $int--;
        $this->test->assertCount($int1, $this->getField("order/user/$int/selection"));
    }

    public function thenUser_ShouldHaveNothingSelectedForSelection($userNum, $selectionNum) {
        $userNum--;
        $selectionNum--;
        $this->test->assertFalse($this->getField("order/user/$userNum/selection/$selectionNum/selected"));
    }

    public function thenTheSelectionOfUser_ShouldBe_WithTheTitle($userNum, $text, $title) {
        $userNum--;
        $this->test->assertEquals($title, $this->getField("order/user/$userNum/selection/0/selected/title"));
        $this->test->assertEquals($text, $this->getField("order/user/$userNum/selection/0/selected/_"));
    }

    protected function getComponentClass() {
        return SelectionsComponent::$CLASS;
    }

    public function thenTheSuccessMessageShouldBe($value) {
        $this->test->assertEquals($value, $this->getField('success'));
    }

    public function thenTheErrorMessageShouldBe($value) {
        $this->test->assertEquals($value, $this->getField('error'));
    }
}