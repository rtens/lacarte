<?php
namespace rtens\lacarte\web\order;


use rtens\lacarte\model\Dish;
use rtens\lacarte\Presenter;
use rtens\lacarte\utils\TimeService;
use rtens\lacarte\web\DefaultResource;

class TodaysDishesResource extends DefaultResource {

    static $CLASS = __CLASS__;

    /** @var TimeService <- */
    public $time;

    /** @var \rtens\lacarte\OrderInteractor <- */
    public $orderInteractor;

    public function doGet() {
        $todaysMenu = $this->orderInteractor->readAllMenusByDate($this->time->today())->toArray();
        if (empty($todaysMenu)) {
            return new Presenter($this, array(
                'nothing' => true,
                'dish' => null
            ));
        }

        return new Presenter($this, array(
            'nothing' => false,
            'dish' => $this->getDishes($todaysMenu)
        ));
    }

    public function getDishes($menus) {
        $todaysDishes = $this->orderInteractor->readDishesByMenuId($menus[0]->id);
        $dishes = array();
        foreach ($todaysDishes as $dish) {
            /** @var Dish $dish */
            $dishes[] = $dish->getTextIn(Dish::LANG_ENGLISH);
        }
        return $dishes;
    }
}