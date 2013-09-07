<?php
namespace spec\rtens\lacarte\fixture\model;

use rtens\lacarte\model\Menu;
use rtens\lacarte\model\Order;
use rtens\lacarte\model\stores\DishStore;
use rtens\lacarte\model\stores\MenuStore;
use rtens\lacarte\model\stores\OrderStore;
use rtens\lacarte\OrderInteractor;
use rtens\mockster\MockFactory;
use spec\rtens\lacarte\fixture\Fixture;
use spec\rtens\lacarte\TestCase;
use watoki\factory\Factory;

class OrderFixture extends Fixture {

    public static $CLASS = __CLASS__;

    /** @var nulL|Order */
    public $foundOrder;

    /** @var array|Order[] */
    private $orders = array();

    public function __construct(TestCase $test, Factory $factory, OrderStore $store, UserFixture $user,
                                MenuStore $menuStore, DishStore $dishStore) {
        parent::__construct($test, $factory);
        $this->orderStore = $store;
        $this->user = $user;
        $this->menuStore = $menuStore;
        $this->dishStore = $dishStore;
    }

    public function givenTheOrder_WithDeadline($name, $deadline) {
        $order = new Order($this->user->getGroup()->id, $name, new \DateTime($deadline));
        $this->orderStore->create($order);
        $this->orders[$name] = $order;
    }

    public function thenThereShouldBe_Orders($int) {
        $this->test->assertCount($int, $this->orderStore->readAll());
    }

    public function thenThereShouldBeAnOrderWithTheName($string) {
        foreach ($this->orderStore->readAll() as $order) {
            if ($order->getName() == $string) {
                $this->foundOrder = $order;
                return;
            }
        }
        $this->test->fail("Order with name $string not found");
    }

    public function thenThisOrderShouldHaveTheDeadline($string) {
        $this->test->assertEquals(new \DateTime($string), $this->foundOrder->getDeadline());
    }

    public function thenThisOrderShouldHave_Menus($int) {
        $this->test->assertCount($int, $this->menuStore->readAllByOrderId($this->foundOrder->id));
    }

    public function thenTheDateOfMenu_OfThisOrderShouldBe($int, $string) {
        $this->test->assertEquals(new \DateTime($string), $this->getMenuOfFoundOrder($int)->getDate());
    }

    /**
     * @param $index
     * @return Menu
     */
    private function getMenuOfFoundOrder($index) {
        $menus = $this->menuStore->readAllByOrderId($this->foundOrder->id);
        return $menus[$index - 1];
    }

    public function thenMenu_OfThisOrderShouldHave_Dishes($menuIndex, $numDishes) {
        $menu = $this->getMenuOfFoundOrder($menuIndex);
        $this->test->assertCount($numDishes, $this->dishStore->readAllByMenuId($menu->id));
    }
}