<?php
namespace rtens\lacarte\web\export;

use rtens\lacarte\OrderInteractor;
use rtens\lacarte\UserInteractor;
use rtens\lacarte\core\Session;
use rtens\lacarte\model\Dish;
use rtens\lacarte\model\Order;
use rtens\lacarte\model\Selection;
use rtens\lacarte\model\User;
use rtens\lacarte\utils\CsvRenderer;
use rtens\lacarte\web\DefaultComponent;
use watoki\curir\Path;
use watoki\curir\Url;
use watoki\curir\controller\Module;
use watoki\factory\Factory;

class DishesComponent extends DefaultComponent {

    static $CLASS = __CLASS__;

    /** @var array|User[] */
    private $userCache = array();

    private $orderInteractor;

    function __construct(Factory $factory, Path $route, Module $parent = null,
                         UserInteractor $userInteractor, Session $session, OrderInteractor $orderInteractor) {
        parent::__construct($factory, $route, $parent, $userInteractor, $session);
        $this->orderInteractor = $orderInteractor;

        $this->rendererFactory->setRenderer('csv', CsvRenderer::$CLASS);
    }

    public function doGet($order) {
        if (!$this->isAdmin()) {
            return $this->redirect(Url::parse('../order/list.html'));
        }

        return array(
            'content' => $this->assembleRows($this->orderInteractor->readById($order))
        );
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
            $users[] = $this->getUser($selection->getUserId())->getName();
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