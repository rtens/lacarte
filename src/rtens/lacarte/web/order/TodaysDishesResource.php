<?php
namespace rtens\lacarte\web\order;


use rtens\lacarte\model\Dish;
use rtens\lacarte\OrderInteractor;
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
        $model = array(
            'dish' => $this->getDishes(),
            'date' => $this->time->today()->format('Y-m-d'),
            'error' => null
        );
        return new Presenter($model);
    }

    public function getDishes() {
        $todaysMenu = $this->orderInteractor->readAllMenusByDate($this->time->today())->toArray();
        if (empty($todaysMenu)) {
            return 'There is no food today :(';
        }
        $todaysDishes = $this->orderInteractor->readDishesByMenuId($todaysMenu[0]->id);
        $dishes = array();
        foreach ($todaysDishes as $dish) {
            /** @var Dish $dish */
            $dishes[] = array(
                '_' => $dish->getTextIn(Dish::LANG_ENGLISH),
                'class' => 'today'
            );
        }
        return $dishes;
    }
}