<?php
namespace rtens\lacarte\web\order;


use rtens\lacarte\core\Session;
use rtens\lacarte\model\Dish;
use rtens\lacarte\OrderInteractor;
use rtens\lacarte\UserInteractor;
use rtens\lacarte\web\DefaultComponent;
use watoki\curir\controller\Module;
use watoki\curir\Path;
use watoki\factory\Factory;

class TodaysDishesComponent extends DefaultComponent {

    /**
     * @var \DateTime
     */
    public $time;

    /**
     * @var \rtens\lacarte\OrderInteractor
     */
    public $orderInteractor;

    function __construct(
        Factory $factory,
        Path $route, Module $parent = null,
        UserInteractor $userInteractor,
        Session $session,
        OrderInteractor $orderInteractor
    ) {
        parent::__construct($factory, $route, $parent, $userInteractor, $session);

        $this->orderInteractor = $orderInteractor;
        $this->time = new \DateTime('next tuesday');

    }

    public function doGet() {
        return $this->assembleModel();
    }

    protected function assembleModel($model = array()) {
        return parent::assembleModel(
            array_merge(
                array(
                    'dish' => $this->getDishes(),
                    'date' => $this->time->format('Y-m-d'),
                    'error' => null
                ),
                $model
            )
        );
    }

    public function getDishes() {
        $todaysMenu = $this->orderInteractor->readAllMenusByDate($this->time)->toArray();
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