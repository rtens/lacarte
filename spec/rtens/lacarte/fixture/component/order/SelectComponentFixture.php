<?php
namespace spec\rtens\lacarte\fixture\component\order;

use rtens\lacarte\model\Order;
use rtens\lacarte\web\LaCarteModule;
use rtens\lacarte\web\order\SelectComponent;
use spec\rtens\lacarte\fixture\component\ComponentFixture;
use spec\rtens\lacarte\fixture\model\OrderFixture;
use spec\rtens\lacarte\fixture\service\SessionFixture;
use spec\rtens\lacarte\fixture\model\UserFixture;
use spec\rtens\lacarte\TestCase;
use watoki\collections\Map;
use watoki\factory\Factory;

/**
 * @property SelectComponent $component
 */
class SelectComponentFixture extends ComponentFixture {

    public static $CLASS = __CLASS__;

    /** @var array */
    private $selections = array();

    /** @var null|Order */
    private $currentOrder;

    /** @var OrderFixture */
    private $order;

    public function __construct(TestCase $test, Factory $factory, UserFixture $user, LaCarteModule $root,
                                SessionFixture $session, OrderFixture $order) {
        parent::__construct($test, $factory, $user, $root, $session);
        $this->order = $order;
    }

    public function whenIOpenThePageForOrder($string) {
        $this->model = $this->component->doGet($this->order->getOrder($string)->id);
    }

    public function whenIOpenThePageForOrderForTheUser($orderName, $userName) {
        $this->model = $this->component->doGet($this->order->getOrder($orderName)->id, $this->user->getUser($userName));
    }

    public function thenTheDisplayedTimeLeftShouldBe($string) {
        $this->test->assertEquals($string, $this->getField('order/timeLeft'));
    }

    public function thenTheSelectionOf_ShouldBeLoaded($string) {
        $this->test->assertEquals($this->user->getUser($string)->id, $this->getField('userId/value'));
    }

    public function thenThereShouldBeNoErrorMessage() {
        $this->thenTheErrorMessageShouldBe(null);
    }

    public function thenThereShouldBeNoSuccessMessage() {
        $this->thenTheSuccessMessageShouldBe(null);
    }

    public function thenTheOrder_ShouldBeLoaded($string) {
        $this->test->assertEquals($this->order->getOrder($string)->id, $this->getField('order/id/value'));
    }

    public function thenThereShouldBe_Menus($int) {
        $this->test->assertCount($int, $this->getField('order/menu'));
    }

    public function thenTheDateOfMenu_ShouldBe($int, $string) {
        $this->then_OfMenu_ShouldBe('date', $int, $string);
    }

    public function thenTheNoneOptionOfMenu_ShouldNotBeChecked($int) {
        $this->then_OfMenu_ShouldBe('none/key/checked', $int, false);
    }

    public function thenTheNoneOptionOfMenu_ShouldBeChecked($int) {
        $this->then_OfMenu_ShouldBe('none/key/checked', $int, "checked");
    }

    public function thenMenu_ShouldHave_Dishes($menuNum, $dishCount) {
        $this->test->assertCount($dishCount, $this->getMenuField('dish', $menuNum));
    }

    public function thenDish_OfMenu_ShouldBe($dishNum, $menuNum, $text) {
        $this->then_OfDish_OfMenu_ShouldBe('text', $dishNum, $menuNum, $text);
    }

    public function thenDish_OfMenu_ShouldNotBeChecked($dishNum, $menuNum) {
        $this->then_OfDish_OfMenu_ShouldBe('key/checked', $dishNum, $menuNum, false);
    }

    public function thenDish_OfMenu_ShouldBeChecked($dishNum, $menuNum) {
        $this->then_OfDish_OfMenu_ShouldBe('key/checked', $dishNum, $menuNum, "checked");
    }

    public function givenIHaveOpenedThePageForOrder($orderName) {
        $this->whenIOpenThePageForOrder($orderName);
        $this->currentOrder = $this->order->getOrder($orderName);
    }

    public function givenISelectedDish_OfMenu($dishText, $menuNum) {
        foreach ($this->getMenuField('dish', $menuNum) as $dish) {
            if ($this->getFieldIn('text', $dish) == $dishText) {
                $matches = array();
                preg_match('/selection\[(\d+)\]/', $this->getFieldIn('key/name', $dish), $matches);
                $this->selections[$matches[1]] = $this->getFieldIn('key/value', $dish);
            }
        }
    }

    public function givenISelectedNoDishOfMenu($int) {
        $dish = $this->getMenuField('none', $int);
        $matches = array();
        preg_match('/selection\[(\d+)\]/', $this->getFieldIn('key/name', $dish), $matches);
        $this->selections[$matches[1]] = $this->getFieldIn('key/value', $dish);
    }

    public function whenISaveMySelections() {
        $this->model = $this->component->doPost($this->currentOrder->id, new Map($this->selections));
    }

    protected function getComponentClass() {
        return SelectComponent::$CLASS;
    }

    public function thenTheErrorMessageShouldBe($value) {
        $this->test->assertEquals($value, $this->getField('error'));
    }

    public function thenTheSuccessMessageShouldBe($value) {
        $this->test->assertEquals($value, $this->getField('success'));
    }

    private function then_OfMenu_ShouldBe($field, $menuNum, $value) {
        $this->test->assertEquals($value, $this->getMenuField($field, $menuNum));
    }

    private function getMenuField($field, $menuNum) {
        $menuNum--;
        return $this->getField("order/menu/$menuNum/$field");
    }

    private function then_OfDish_OfMenu_ShouldBe($field, $dishNum, $menuNum, $value) {
        $dishNum--;
        $this->then_OfMenu_ShouldBe("dish/$dishNum/$field", $menuNum, $value);
    }
}