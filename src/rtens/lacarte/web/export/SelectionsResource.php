<?php
namespace rtens\lacarte\web\export;
 
use rtens\lacarte\core\NotFoundException;
use rtens\lacarte\model\Dish;
use rtens\lacarte\model\Group;
use rtens\lacarte\model\Menu;
use rtens\lacarte\Presenter;
use rtens\lacarte\WebResource;
use watoki\curir\resource\DynamicResource;

class SelectionsResource extends DynamicResource {

    public static $CLASS = __CLASS__;

    /** @var \rtens\lacarte\core\Configuration <- */
    private $configuration;

    /** @var \rtens\lacarte\utils\TimeService <- */
    private $time;

    /** @var \rtens\lacarte\OrderInteractor <- */
    private $orderInteractor;

    /** @var \rtens\lacarte\UserInteractor <- */
    private $userInteractor;

    /** @var \rtens\lacarte\core\FileRepository <- */
    private $files;

    public function doGet($token, $date = null) {
        if ($token != $this->configuration->getApiToken()) {
            return new Presenter($this->assembleModel(array(
                'error' => 'Wrong token.'
            )));
        }

        if ($date) {
            try {
                $date = new \DateTime($date);
            } catch (\Exception $e) {
                return new Presenter($this->assembleModel(array(
                    'error' => 'Could not parse date.'
                )));
            }
        } else {
            $date = $this->time->fromString('today');
        }

        $menus = $this->orderInteractor->readAllMenusByDate($date);

        if ($menus->isEmpty()) {
            return new Presenter($this->assembleModel(array(
                'error' => 'No menu found for given date.'
            )));
        } else if ($menus->count() > 1) {
            return new Presenter($this->assembleModel(array(
                'error' => 'More than one menu found for given date.'
            )));
        }

        return new Presenter($this->assembleModel(array(
            'menu' => $this->assembleDishes($menus->one()),
            'selections' => $this->assembleSelections($menus->one())
        )));
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
						'avatar' => $this->files->getUserAvatarUrl($user, $this->getAncestor(WebResource::$CLASS)),
						'yielded' => false
                    )
                );
            } catch (NotFoundException $e) {}
        }
        return $selections;
    }

}
