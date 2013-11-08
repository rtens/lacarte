<?php
namespace rtens\lacarte\web\export;

use rtens\lacarte\core\NotFoundException;
use rtens\lacarte\model\Order;
use rtens\lacarte\model\Selection;
use rtens\lacarte\model\User;
use rtens\lacarte\Presenter;
use rtens\lacarte\web\DefaultResource;
use watoki\curir\http\Url;
use watoki\curir\responder\Redirecter;

class DishesResource extends DefaultResource {

    static $CLASS = __CLASS__;

    /** @var array|User[] */
    private $userCache = array();

    /** @var \rtens\lacarte\OrderInteractor <- */
    private $orderInteractor;

    public function doGet($order) {
        if (!$this->isAdmin()) {
            return new Redirecter(Url::parse('../order/list.html'));
        }

        return new Presenter(array(
            'content' => $this->assembleRows($this->orderInteractor->readById($order))
        ));
    }

    private function assembleRows(Order $order) {
        $rows = array();
        foreach ($this->orderInteractor->readMenusByOrderId($order->id) as $menu) {
            foreach ($this->orderInteractor->readDishesByMenuId($menu->id) as $dish) {
                $selections = $this->orderInteractor->readAllSelectionsByDishId($dish->id);

                $rows[] = array(
                    'date' => $menu->getDate()->format('Y-m-d'),
                    'dish' => $dish->getText(),
                    'sum' => count($selections),
                    'by' => implode(', ', $this->getUserNames($selections))
                );
            }
        }
        return $rows;
    }

    /**
     * @param Selection[] $selections
     * @return array
     */
    private function getUserNames($selections) {
        $users = array();
        foreach ($selections as $selection) {
            try {
                $users[] = $this->getUser($selection->getUserId())->getName();
            } catch (NotFoundException $e) {
                $users[] = 'Deleted';
            }
        }
        return $users;
    }

    private function getUser($userId) {
        if (!isset($this->userCache[$userId])) {
            $this->userCache[$userId] = $this->userInteractor->readById($userId);
        }
        return $this->userCache[$userId];
    }

}