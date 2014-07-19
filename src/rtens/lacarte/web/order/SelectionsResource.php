<?php
namespace rtens\lacarte\web\order;

use rtens\lacarte\core\NotFoundException;
use rtens\lacarte\model\Dish;
use rtens\lacarte\model\Group;
use rtens\lacarte\model\Menu;
use rtens\lacarte\model\Order;
use rtens\lacarte\model\User;
use rtens\lacarte\Presenter;
use rtens\lacarte\web\DefaultResource;
use watoki\curir\http\Url;
use watoki\curir\responder\Redirecter;

class SelectionsResource extends DefaultResource {

    static $CLASS = __CLASS__;

    /** @var Dish[] */
    private $dishes = array();

    /** @var Menu[] Cache for Menus */
    private $menus = array();

    /** @var \rtens\lacarte\OrderInteractor <- */
    private $orderInteractor;

    public function doGet($order) {
        if (!$this->isAdmin()) {
            return new Redirecter(Url::parse('list.html'));
        }

        return new Presenter($this, $this->assembleMyModel($order));
    }

    public function doSendMail($order, $subject, $body, $onlyWithout = false) {
        if (!$this->isAdmin()) {
            return new Redirecter(Url::parse('list.html'));
        }

        $emailModel = array(
            'subject' => array(
                'value' => $subject
            ),
            'body' => $body,
            'onlyWithout' => array(
                'checked' => $onlyWithout ? 'checked' : false
            )
        );

        if (!trim($subject) || !trim($body)) {
            return new Presenter($this, $this->assembleMyModel($order, array(
                'error' => 'Please fill out subject and body to send an email',
                'email' => $emailModel
            )));
        }

        try {
            $this->orderInteractor->sendMail($this->orderInteractor->readById($order), $subject, $body, $onlyWithout);
            return new Presenter($this, $this->assembleMyModel($order, array(
                'success' => 'Email was sent to users' . ($onlyWithout ? ' without selections' : '')
            )));
        } catch (\Exception $e) {
            return new Presenter($this, $this->assembleMyModel($order, array(
                'error' => $e->getMessage(),
                'email' => $emailModel
            )));
        }
    }

    private function assembleMyModel($orderId, $model = array()) {
        return $this->assembleModel(array_merge(array(
            'actions' => $this->assembleActions($orderId),
            'order' => $this->assembleOrder($this->orderInteractor->readById($orderId)),
            'error' => null,
            'success' => null
        ), $model));
    }

    private function assembleActions($orderId) {
        return array(
            'edit' => $this->createLink('edit.html', $orderId),
            'exportByDish' => $this->createLink('../export/dishes.csv', $orderId),
            'exportByUser' => null,
        );
    }

    private function createLink($component, $orderId) {
        return array(
            'href' => $component . '?order=' . $orderId
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