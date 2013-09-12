<?php
namespace spec\rtens\lacarte\fixtures\component\order;

use rtens\lacarte\model\Order;
use rtens\lacarte\model\stores\DishStore;
use rtens\lacarte\model\stores\MenuStore;
use rtens\lacarte\web\LaCarteModule;
use rtens\lacarte\web\order\EditComponent;
use spec\rtens\lacarte\fixtures\component\ComponentFixture;
use spec\rtens\lacarte\fixtures\model\OrderFixture;
use spec\rtens\lacarte\fixtures\service\SessionFixture;
use spec\rtens\lacarte\fixtures\model\UserFixture;
use spec\rtens\lacarte\TestCase;
use watoki\factory\Factory;

/**
 * @property EditComponent $component
 */
class EditComponentFixture extends ComponentFixture {

    public static $CLASS = __CLASS__;

    /** @var OrderFixture */
    private $order;

    /** @var array|string[] */
    private $dishTexts = array();

    /** @var DishStore */
    private $dishStore;

    /** @var null|Order */
    private $currentOrder;

    /** @var MenuStore */
    private $menuStore;

    public function __construct(TestCase $test, Factory $factory, LaCarteModule $root, MenuStore $menuStore,
                                DishStore $dishStore) {
        parent::__construct($test, $factory, $root);
        $this->order = $test->useFixture(OrderFixture::$CLASS);
        $this->menuStore = $menuStore;
        $this->dishStore = $dishStore;
    }

    public function whenIOpenThePageToEdit($orderName) {
        $this->model = $this->component->doGet($this->order->getOrder($orderName)->id);
    }

    public function whenISaveTheOrder() {
        $this->model = $this->component->doPost($this->currentOrder->id, $this->dishTexts);
    }

    public function thenThereShouldBeNoErrorMessage() {
        $this->thenTheErrorMessageShouldBe(null);
    }

    public function thenTheErrorMessageShouldBe($string) {
        $this->test->assertEquals($string, $this->getField('error'));
    }

    public function thenThereShouldBeNoSuccessMessage() {
        $this->thenTheSuccessMessageShouldBe(null);
    }

    public function thenTheSuccessMessageShouldBe($string) {
        $this->test->assertEquals($string, $this->getField('success'));
    }

    public function thenTheNameOfTheOrderShouldBe($string) {
        $this->test->assertEquals($string, $this->getField('order/name'));
        $this->test->assertEquals($this->order->getOrder($string)->id, $this->getField('order/id/value'));
    }

    public function thenThereShouldBe_Menus($int) {
        $this->test->assertCount($int, $this->getField('order/menu'));
    }

    public function thenTheDateOfMenu_ShouldBe($num, $string) {
        $num--;
        $this->test->assertEquals($string, $this->getField("order/menu/$num/date"));
    }

    public function thenMenu_ShouldHave_Dishes($menuNum, $countDishes) {
        $menuNum--;
        $this->test->assertCount($countDishes, $this->getField("order/menu/$menuNum/dish"));
    }

    public function thenDish_OfMenu_ShouldBe($dishNum, $menuNum, $text) {
        $menuNum--;
        $dishNum--;
        $id = $this->order->getDish($text)->id;
        $this->test->assertEquals($text, $this->getField("order/menu/$menuNum/dish/$dishNum/text/_"));
        $this->test->assertEquals("dish[$id]", $this->getField("order/menu/$menuNum/dish/$dishNum/text/name"));
    }

    public function givenIHaveEntered_ForDish_OfMenu($text, $dishNum, $menuNum) {
        $menus = $this->menuStore->readAllByOrderId($this->currentOrder->id);
        $dishes = $this->dishStore->readAllByMenuId($menus[$menuNum - 1]->id);
        $this->dishTexts[$dishes[$dishNum - 1]->id] = $text;
    }

    public function givenIHaveOpenedThePageToEdit($orderName) {
        $this->currentOrder = $this->order->getOrder($orderName);
    }

    protected function getComponentClass() {
        return EditComponent::$CLASS;
    }
}