<?php
namespace rtens\lacarte\web\order;

use rtens\lacarte\OrderInteractor;
use rtens\lacarte\UserInteractor;
use rtens\lacarte\core\NotFoundException;
use rtens\lacarte\core\Session;
use rtens\lacarte\model\Menu;
use rtens\lacarte\model\Order;
use rtens\lacarte\model\Selection;
use rtens\lacarte\utils\TimeService;
use rtens\lacarte\web\DefaultComponent;
use watoki\collections\Map;
use watoki\collections\Set;
use watoki\curir\Path;
use watoki\curir\Url;
use watoki\curir\controller\Module;
use watoki\factory\Factory;

class SelectComponent extends DefaultComponent {

    public static $CLASS = __CLASS__;

    /** @var array|Selection[] */
    private $selections = array();

    private $time;

    private $orderInteractor;

    /**
     * @param int $order ID of Order
     * @param Map|int[] $selection Selected dish IDs indexed by menu ID
     * @param null|int $user ID of user (if admin)
     * @param int|null $user Set if admin is changing the selection
     * @return array|null
     */
    public function doPost($order, Map $selection = null, $user = null) {
        $orderEntity = $this->orderInteractor->readById($order);
        $userId = $this->getUserId($user);

        if ($selection) {
            try {
                $selections = $this->collectSelections($order, $selection, $user, $userId);
                $this->orderInteractor->saveSelections($selections);

                return $this->assembleMyModel($orderEntity, $userId, array(
                    'success' => 'Selection saved'
                ));
            } catch (\InvalidArgumentException $e) {}
        }

        return $this->assembleMyModel($orderEntity, $userId, array(
            'error' => 'Please make a selection for every day'
        ));
    }
    private function collectSelections($order, Map $selection, $user, $userId) {
        $missing = false;
        $selections = new Set();
        foreach ($this->orderInteractor->readMenusByOrderId($order) as $menu) {
            if (!$selection->has($menu->id)) {
                $missing = true;
                continue;
            }
            $dishId = $selection->get($menu->id);
            try {
                $selectionEntity = $this->orderInteractor->readSelectionByMenuIdAndUserId($menu->id, $userId);
                $selectionEntity->setDishId($dishId);
            } catch (NotFoundException $e) {
                $selectionEntity = new Selection($this->getUserId($user), $menu->id, $dishId);
            }
            $this->selections[$menu->id] = $selectionEntity;
            $selections->put($selectionEntity);
        }
        if ($missing) {
            throw new \InvalidArgumentException('Missing selection');
        }

        return $selections;
    }

    function __construct(Factory $factory, Path $route, Module $parent = null,
                         UserInteractor $userInteractor, Session $session, OrderInteractor $orderInteractor,
                         TimeService $time) {
        parent::__construct($factory, $route, $parent, $userInteractor, $session);
        $this->orderInteractor = $orderInteractor;
        $this->time = $time;
    }

    public function doGet($order, $user = null) {
        $entity = $this->orderInteractor->readById($order);

        if ($this->isOpen($entity)) {
            return $this->redirect(Url::parse('selection.html?order=' . $entity->id));
        }

        return $this->assembleMyModel($entity, $user);
    }

    private function assembleMyModel(Order $order, $userId, $model = array()) {
        return $this->assembleModel(array_merge(array(
            'error' => null,
            'success' => null,
            'order' => $this->assembleOrder($order, $userId),
            'userId' => array(
                'value' => $userId
            )
        ), $model));
    }

    private function assembleOrder(Order $order, $userId) {
        $sign = $this->time->now() > $order->getDeadline() ? '-' : '';
        return array(
            'id' => array(
                'value' => $order->id
            ),
            'timeLeft' => $sign . $this->time->until($order->getDeadline())->format('%dd %hh %im'),
            'menu' => $this->assembleMenus($order, $userId)
        );
    }

    private function assembleMenus(Order $order, $userId) {
        $menus = array();
        foreach ($this->orderInteractor->readMenusByOrderId($order->id) as $menu) {
            $dishId = $this->getDishId($menu->id, $userId);

            $menus[] = array(
                'date' => $menu->getDate()->format('l, j.n.Y'),
                'none' => array(
                    'key' => $this->assembleKey($menu, $dishId !== null && intval($dishId) === 0)
                ),
                'dish' => $this->assembleDishes($menu, intval($dishId))
            );
        }
        return $menus;
    }

    private function getDishId($menuId, $userId) {
        if (array_key_exists($menuId, $this->selections)) {
            return $this->selections[$menuId]->getDishId();
        }
        try {
            return $this->orderInteractor
                ->readSelectionByMenuIdAndUserId($menuId, $this->getUserId($userId))
                ->getDishId();
        } catch (NotFoundException $e) {
            return null;
        }
    }

    private function assembleKey(Menu $menu, $checked, $value = 0) {
        return array(
            'name' => "selection[{$menu->id}]",
            'value' => $value,
            'checked' => $checked ? "checked" : false
        );
    }

    private function assembleDishes(Menu $menu, $selectedDishId) {
        $dishes = array();
        foreach ($this->orderInteractor->readDishesByMenuId($menu->id) as $dish) {
            $dishes[] = array(
                'key' => $this->assembleKey($menu, $selectedDishId === $dish->id, $dish->id),
                'text' => $dish->getText()
            );
        }
        return $dishes;
    }

    private function isOpen(Order $order) {
        return !$this->isAdmin() && $order->getDeadline() < $this->time->now();
    }

    private function getUserId($userId) {
        if ($this->isAdmin() && $userId) {
            return $userId;
        } else if ($this->isLoggedIn()) {
            return $this->getLoggedInUser()->id;
        } else {
            throw new \Exception('Could not determine user');
        }
    }

}