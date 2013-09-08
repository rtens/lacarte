<?php
namespace spec\rtens\lacarte\fixtures\model;

use rtens\lacarte\model\stores\MenuStore;
use rtens\lacarte\model\stores\SelectionStore;
use rtens\mockster\MockFactory;
use spec\rtens\lacarte\fixtures\Fixture;
use spec\rtens\lacarte\TestCase;
use watoki\factory\Factory;

class SelectionFixture extends Fixture {

    public static $CLASS = __CLASS__;

    private $user;

    private $menuStore;

    private $store;

    private $order;

    public function __construct(TestCase $test, Factory $factory, SelectionStore $store, MenuStore $menuStore,
                                OrderFixture $order, UserFixture $user) {
        parent::__construct($test, $factory);
        $this->store = $store;
        $this->menuStore = $menuStore;
        $this->order = $order;
        $this->user = $user;
    }

    public function thenThereShouldBe_Selections($int) {
        $this->test->assertCount($int, $this->store->readAll());
    }

    public function thenThereShouldBeASelectionWithMenu_OfOrder_AndDish_ForUser($menuNum, $order, $dishText, $userName) {
        $menus = $this->menuStore->readAllByOrderId($this->order->getOrder($order)->id);

        $menuId = $menus[$menuNum - 1]->id;
        $userId = $this->user->getUser($userName)->id;
        $dishId = $this->order->getDish($dishText)->id;

        foreach ($this->store->readAll() as $selection) {
            if ($selection->getMenuId() == $menuId
                && $selection->getUserId() == $userId
                && $selection->getDishId() == $dishId
            ) {
                return;
            }
        }
        $this->test->fail("Could not find selection");
    }

    public function thenThereShouldBeASelectionWithMenu_OfOrder_AndNoDishForUser($menuNum, $order, $userName) {
        $menus = $this->menuStore->readAllByOrderId($this->order->getOrder($order)->id);
        $menu = $menus[$menuNum - 1];
        foreach ($this->store->readAll() as $selection) {
            if ($selection->getMenuId() == $menu->id
                && $selection->getUserId() == $this->user->getUser($userName)->id
                && !$selection->getDishId()
            ) {
                return;
            }
        }
        $this->test->fail("Could not find selection");
    }

}