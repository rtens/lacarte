<?php
namespace spec\rtens\lacarte\fixtures\model;

use rtens\lacarte\model\stores\MenuStore;
use rtens\lacarte\model\stores\SelectionStore;
use rtens\mockster\MockFactory;
use watoki\scrut\Fixture;

/**
 * @property OrderFixture order <-
 * @property UserFixture user <-
 * @property SelectionStore store <-
 * @property MenuStore menuStore <-
 */
class SelectionFixture extends Fixture {

    public static $CLASS = __CLASS__;

    public function thenThereShouldBe_Selections($int) {
        $this->spec->assertCount($int, $this->store->readAll());
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
        $this->spec->fail("Could not find selection");
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
        $this->spec->fail("Could not find selection");
    }

}