<?php
namespace spec\rtens\lacarte\features\order;

use rtens\lacarte\OrderInteractor;
use rtens\lacarte\model\Dish;
use rtens\lacarte\model\Menu;
use rtens\lacarte\model\Order;
use rtens\lacarte\model\stores\DishStore;
use rtens\lacarte\model\stores\MenuStore;
use rtens\lacarte\model\stores\OrderStore;
use spec\rtens\lacarte\Test;
use spec\rtens\lacarte\Test_Given;
use spec\rtens\lacarte\Test_Then;
use spec\rtens\lacarte\Test_When;
use watoki\collections\Set;

/**
 * @property SaveOrderTest_Given given
 * @property SaveOrderTest_When when
 * @property SaveOrderTest_Then then
 */
class SaveOrderTest extends Test {

    function testSaveAllDishes() {
        $this->given->anOrderWith_MenusAnd_DishesEach(2, 2);
        $this->given->dish_OfMenu_Is(1, 1, 'A');
        $this->given->dish_OfMenu_Is(2, 1, 'B');
        $this->given->dish_OfMenu_Is(1, 2, 'C');
        $this->given->dish_OfMenu_Is(2, 2, 'D');

        $this->when->iSaveTheOrder();

        $this->then->thereShouldBe_Dishes(4);
        $this->then->dish_ShouldBe(1, 'A');
        $this->then->dish_ShouldBe(2, 'B');
        $this->then->dish_ShouldBe(3, 'C');
        $this->then->dish_ShouldBe(4, 'D');
    }

    function testEmptyDishes() {
        $this->given->anOrderWith_MenusAnd_DishesEach(2, 2);
        $this->given->dish_OfMenu_Is(1, 1, 'A');
        $this->given->dish_OfMenu_Is(2, 1, '');
        $this->given->dish_OfMenu_Is(1, 2, '');
        $this->given->dish_OfMenu_Is(2, 2, 'D');

        $this->when->iSaveTheOrder();

        $this->then->thereShouldBe_Dishes(2);
        $this->then->dish_ShouldBe(1, 'A');
        $this->then->dish_ShouldBe(4, 'D');
    }

}

class SaveOrderTest_Given extends Test_Given {

    /** @var array|array[]|Dish[][] */
    public $dishes;

    /** @var Order|null */
    public $order;

    function __construct(Test $test, OrderStore $orderStore, MenuStore $menuStore, DishStore $dishStore) {
        parent::__construct($test);
        $this->orderStore = $orderStore;
        $this->menuStore = $menuStore;
        $this->dishStore = $dishStore;
    }

    public function anOrderWith_MenusAnd_DishesEach($numMenus, $numDishes) {
        $this->order = new Order(1, 'test', new \DateTime('2000-01-01'));
        $this->orderStore->create($this->order);

        $this->dishes = array();
        for ($m = 0; $m < $numMenus; $m++) {
            $menu = new Menu($this->order->id, new \DateTime('2000-01-' . $m));
            $this->menuStore->create($menu);

            $dishes = array();
            for ($d = 0; $d < $numDishes; $d++) {
                $dish = new Dish($menu->id, '');
                $this->dishStore->create($dish);
                $dishes[] = $dish;
            }
            $this->dishes[] = $dishes;
        }
    }

    public function dish_OfMenu_Is($numDish, $numMenu, $text) {
        /** @var Dish $dish */
        $dish = $this->dishes[$numMenu - 1][$numDish - 1];
        $dish->setText($text);
    }
}

/**
 * @property SaveOrderTest test
 */
class SaveOrderTest_When extends Test_When {

    function __construct(Test $test, OrderInteractor $interactor) {
        parent::__construct($test);
        $this->interactor = $interactor;
    }

    public function iSaveTheOrder() {
        $dishes = new Set();
        foreach ($this->test->given->dishes as $menu) {
            foreach ($menu as $dish) {
                $dishes[] = $dish;
            }
        }
        $this->interactor->updateDishes($dishes);
    }
}

class SaveOrderTest_Then extends Test_Then {

    function __construct(Test $test, DishStore $dishStore) {
        parent::__construct($test);
        $this->dishStore = $dishStore;
    }

    public function thereShouldBe_Dishes($count) {
        $this->test->assertEquals($count, $this->dishStore->readAll()->count());
    }

    public function dish_ShouldBe($id, $text) {
        $this->test->assertEquals($text, $this->dishStore->readById($id)->getText());
    }
}