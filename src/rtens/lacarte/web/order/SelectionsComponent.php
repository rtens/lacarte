<?php
namespace rtens\lacarte\web\order;

use rtens\lacarte\OrderInteractor;
use rtens\lacarte\UserInteractor;
use rtens\lacarte\core\NotFoundException;
use rtens\lacarte\core\Session;
use rtens\lacarte\model\Dish;
use rtens\lacarte\model\Group;
use rtens\lacarte\model\Menu;
use rtens\lacarte\model\Order;
use rtens\lacarte\model\User;
use rtens\lacarte\web\DefaultComponent;
use watoki\curir\Path;
use watoki\curir\Url;
use watoki\curir\controller\Module;
use watoki\factory\Factory;

class SelectionsComponent extends DefaultComponent {

    static $CLASS = __CLASS__;

    /** @var Dish[] */
    private $dishes = array();

    /** @var Menu[] Cache for Menus */
    private $menus = array();

    private $orderInteractor;

    function __construct(Factory $factory, Path $route, Module $parent = null,
                         UserInteractor $userInteractor, Session $session, OrderInteractor $orderInteractor) {
        parent::__construct($factory, $route, $parent, $userInteractor, $session);
        $this->orderInteractor = $orderInteractor;
    }

    public function doGet($order) {
        if (!$this->isAdmin()) {
            return $this->redirect(Url::parse('list.html'));
        }

        return $this->assembleModel(array(
            'actions' => $this->assembleActions($order),
            'order' => $this->assembleOrder($this->orderInteractor->readById($order)),
            'email' => null
        ));
    }

    private function assembleActions($orderId) {
        return array(
            'edit' => $this->createLink('edit', $orderId),
            'exportByDish' => null,
            'exportByUser' => null,
        );
    }

    private function createLink($component, $orderId) {
        return array(
            'href' => $component . '.html?order=' . $orderId
        );
    }

    private function assembleOrder(Order $order) {
        return array(
            'id' => array('value' => $order->id),
            'name' => $order->getName(),
            'date' => $this->assembleMenuDates($order),
            'user' => $this->assembleUsersAndSelections($order),
        );
    }

    private function assembleMenuDates(Order $order) {
        $dates = array();
        foreach ($this->getMenus($order) as $menu) {
            $dates[] = $menu->getDate()->format('D');
        }
        return $dates;
    }

    private function assembleUsersAndSelections(Order $order) {
        $group = new Group('', '', '');
        $group->id = $this->getAdminGroupId();

        $users = array();
        foreach ($this->userInteractor->readAllByGroup($group) as $user) {
            $users[] = array(
                'selectLink' => array(
                    'href' => 'select.html?order=' . $order->id . '&user=' . $user->id
                ),
                'name' => $user->getName(),
                'selection' => $this->assembleSelections($order, $user)
            );
        }
        return $users;
    }

    private function assembleSelections(Order $order, User $user) {
        $selections = array();
        foreach ($this->getMenus($order) as $menu) {
            try {
                $selection = $this->orderInteractor->readSelectionByMenuIdAndUserId($menu->id, $user->id);

                if ($selection->hasDish()) {
                    $dish = $this->getDish($selection->getDishId());
                    $selected = array(
                        'title' => $dish->getText(),
                        '_' => strlen($dish->getText()) <= 5
                            ? $dish->getText()
                            : trim(substr($dish->getText(), 0, 5)) . '...'

                    );
                } else {
                    $selected = array(
                        'title' => 'nothing',
                        '_' => '-'
                    );
                }
            } catch (NotFoundException $e) {
                $selected = false;
            }
            $selections[] = array(
                'selected' => $selected
            );
        }
        return $selections;
    }

    private function getMenus(Order $order) {
        if (!$this->menus) {
            $this->menus = $this->orderInteractor->readMenusByOrderId($order->id);
        }
        return $this->menus;
    }

    /**
     * @param $dishId
     * @return \rtens\lacarte\model\Dish
     */
    private function getDish($dishId) {
        if (!isset($this->dishes[$dishId])) {
            $this->dishes[$dishId] = $this->orderInteractor->readDishById($dishId);
        }
        return $this->dishes[$dishId];
    }

}