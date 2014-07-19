<?php
namespace rtens\lacarte\web\order;

use rtens\lacarte\core\NotFoundException;
use rtens\lacarte\Presenter;
use rtens\lacarte\web\DefaultResource;
use watoki\curir\http\Url;
use watoki\curir\responder\Redirecter;

class ListResource extends DefaultResource {

    public static $CLASS = __CLASS__;

    /** @var \rtens\lacarte\utils\TimeService <- */
    protected $time;

    /** @var \rtens\lacarte\OrderInteractor <- */
    protected $orderInteractor;

    public function doGet() {
        return new Presenter($this, $this->assembleModel());
    }

    public function doPost($firstDay, $lastDay, $deadline) {
        if (!$this->isAdmin()) {
            return new Presenter($this, $this->assembleModel(
                array(
                    'error' => 'Access denied.'
                )
            ));
        }
        try {
            $order = $this->orderInteractor->createOrder(
                $this->getAdminGroupId(),
                new \DateTime($firstDay),
                new \DateTime($lastDay),
                new \DateTime($deadline)
            );
            return new Redirecter(Url::parse('edit.html?order=' . $order->id));
        } catch (\Exception $e) {
            return new Presenter($this, $this->assembleModel(
                array(
                    'error' => $e->getMessage()
                )
            ));
        }
    }

    protected function assembleModel($model = array()) {
        return parent::assembleModel(
            array_merge(
                array(
                    'order' => $this->assembleOrders(),
                    'firstDay' => array('value' => $this->time->fromString('monday next week')->format('Y-m-d')),
                    'lastDay' => array('value' => $this->time->fromString('friday next week')->format('Y-m-d')),
                    'deadline' => array(
                        'value' => $this->time->fromString('thursday this week 18:00')->format(
                                'Y-m-d H:i'
                            )
                    ),
                    'error' => null,
                    'today' => $this->getTodaysOrder()
                ),
                $model
            )
        );
    }

    private function assembleOrders() {
        $orders = array();
        foreach ($this->orderInteractor->readAll() as $order) {
            $orders[] = array(
                'name' => $order->getName(),
                'deadline' => $order->getDeadline()->format('d.m.Y H:i'),
                'isOpen' => $order->getDeadline() > $this->time->now(),
                'itemLink' => $this->makeLink($this->isAdmin() ? 'selections' : 'select', $order),
                'editLink' => $this->makeLink('edit', $order),
                'selectLink' => $this->makeLink('select', $order),
            );
        }
        return $orders;
    }

    private function makeLink($component, $order) {
        return array(
            'href' => $component . '.html?order=' . $order->id
        );
    }

    private function getTodaysOrder() {
        if (!$this->isUser()) {
            return null;
        }

        $menus = $this->orderInteractor->readAllMenusByDate($this->time->fromString('today'));
        foreach ($menus as $menu) {
            try {
                $selections = $this->orderInteractor->readSelectionByMenuIdAndUserId(
                    $menu->id,
                    $this->getLoggedInUser()->id
                );
            } catch (NotFoundException $e) {
                return null;
            }

            if ($selections->getDishId() == 0) {
                return array('dish' => 'Nothing for you today.');
            }

            $dish = $this->orderInteractor->readDishById($selections->getDishId())->getText();
            return array('dish' => $dish);
        }
        return null;
    }

}
