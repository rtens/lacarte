<?php
namespace rtens\lacarte\web\export;
 
use rtens\lacarte\OrderInteractor;
use rtens\lacarte\UserInteractor;
use rtens\lacarte\core\Configuration;
use rtens\lacarte\core\NotFoundException;
use rtens\lacarte\model\Dish;
use rtens\lacarte\model\Group;
use rtens\lacarte\model\Menu;
use rtens\lacarte\utils\TimeService;
use watoki\curir\Path;
use watoki\curir\controller\Component;
use watoki\curir\controller\Module;
use watoki\factory\Factory;

class SelectionsComponent extends Component {

    public static $CLASS = __CLASS__;

    private $configuration;

    private $time;

    private $orderInteractor;

    private $userInteractor;

    function __construct(Factory $factory, Path $route, Module $parent = null, Configuration $config,
                         TimeService $time, OrderInteractor $orderInteractor, UserInteractor $userInteractor) {
        parent::__construct($factory, $route, $parent);
        $this->configuration = $config;
        $this->time = $time;
        $this->orderInteractor = $orderInteractor;
        $this->userInteractor = $userInteractor;
    }

    public function doGet($token, $date = null) {
        if ($token != $this->configuration->getApiToken()) {
            return $this->assembleModel(array(
                'error' => 'Wrong token.'
            ));
        }

        if ($date) {
            try {
                $date = new \DateTime($date);
            } catch (\Exception $e) {
                return $this->assembleModel(array(
                    'error' => 'Could not parse date.'
                ));
            }
        } else {
            $date = $this->time->fromString('today');
        }

        $menus = $this->orderInteractor->readAllMenusByDate($date);

        if ($menus->isEmpty()) {
            return $this->assembleModel(array(
                'error' => 'No menu found for given date.'
            ));
        } else if ($menus->count() > 1) {
            return $this->assembleModel(array(
                'error' => 'More than one menu found for given date.'
            ));
        }

        return $this->assembleModel(array(
            'menu' => $this->assembleDishes($menus->one()),
            'selections' => $this->assembleSelections($menus->one())
        ));
    }

    private function assembleModel($model = array()) {
        return array_merge(array(
            'menu' => array(),
            'selections' => array(),
            'error' => null
        ), $model);
    }

    private function assembleDishes(Menu $menu) {
        $dishes = array();
        foreach ($this->orderInteractor->readDishesByMenuId($menu->id) as $dish) {
            $dishes[$dish->id] = array(
                'en' => $dish->getTextIn(Dish::LANG_ENGLISH),
                'de' => $dish->getTextIn(Dish::LANG_GERMAN)
            );
        }
        return array(
            'date' => $menu->getDate()->format('Y-m-d'),
            'dishes' => $dishes
        );
    }

    private function assembleSelections(Menu $menu) {
        $selections = array();

        $order = $this->orderInteractor->readById($menu->getOrderId());

        $group = new Group('', '', '');
        $group->id = $order->getGroupId();

        foreach ($this->userInteractor->readAllByGroup($group) as $user) {
            try {
                $selection = $this->orderInteractor->readSelectionByMenuIdAndUserId($menu->id, $user->id);

                if (!$selection->hasDish()) {
                    continue;
                }

                $selections[$selection->id] = array(
                    'dish' => intval($selection->getDishId()),
                    'user' => array(
                        'id' => $user->id,
                        'name' => $user->getName(),
						'avatar' => $this->configuration->getHost() . '/user/avatars/default.png',
						'yielded' => false
                    )
                );
            } catch (NotFoundException $e) {}
        }
        return $selections;
    }

}
