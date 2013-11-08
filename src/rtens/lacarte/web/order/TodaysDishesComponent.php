<?php
namespace rtens\lacarte\web\order;


use rtens\lacarte\model\Dish;
use rtens\lacarte\OrderInteractor;
use rtens\lacarte\web\DefaultResource;

class TodaysDishesComponent extends DefaultResource {

    static $CLASS = __CLASS__;

    /** @var \rtens\lacarte\OrderInteractor <- */
    public $orderInteractor;

    public function doGet() {
        return $this->assembleModel();
    }

    protected function assembleModel($model = array()) {
        $nextTuesday = new \DateTime('next tuesday');

        return parent::assembleModel(
            array_merge(
                array(
                    'dish' => $this->getDishes(),
                    'date' => $nextTuesday->format('Y-m-d'),
                    'error' => null
                ),
                $model
            )
        );
    }

    public function getDishes() {
        $todaysMenu = $this->orderInteractor->readAllMenusByDate(new \DateTime('next tuesday'))->toArray();
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