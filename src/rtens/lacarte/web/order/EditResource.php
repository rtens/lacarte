<?php
namespace rtens\lacarte\web\order;
 
use rtens\lacarte\model\Dish;
use rtens\lacarte\model\Menu;
use rtens\lacarte\model\Order;
use rtens\lacarte\Presenter;
use rtens\lacarte\web\DefaultResource;
use watoki\collections\Set;
use watoki\curir\http\Url;
use watoki\curir\responder\Redirecter;

class EditResource extends DefaultResource {

    public static $CLASS = __CLASS__;

    /** @var \rtens\lacarte\OrderInteractor <- */
    private $orderInteractor;

    public function doGet($order) {
        if (!$this->isAdmin()) {
            return new Redirecter(Url::parse('list.html'));
        }
        return new Presenter($this, $this->assembleMyModel($order));
    }

    public function doPost($order, $dish) {
        if (!$this->isAdmin()) {
            return new Redirecter(Url::parse('list.html'));
        }

        $dishes = new Set();
        foreach ($dish as $id => $text) {
            $entity = $this->orderInteractor->readDishById($id);
            $entity->setText($text);
            $dishes->put($entity);
        }

        try {
            $this->orderInteractor->updateDishes($dishes);
            return new Presenter($this, $this->assembleMyModel($order, array(
                'success' => 'Order saved'
            )));
        } catch (\Exception $e) {
            return new Presenter($this, $this->assembleMyModel($order, array(
                'error' => $e->getMessage()
            )));
        }
    }

    protected function assembleMyModel($orderId, $model = array()) {
        return $this->assembleModel(array_merge(array(
            'error' => null,
            'success' => null,
            'order' => $this->assembleOrder($this->orderInteractor->readById($orderId))
        ), $model));
    }

    private function assembleOrder(Order $order) {
        $menus = $this->orderInteractor->readMenusByOrderId($order->id);
        return array(
            'id' => array('value' => $order->id),
            'name' => $order->getName(),
            'menu' => $this->assembleMenus($menus)
        );
    }

    /**
     * @param Menu[] $menus
     * @return array
     */
    private function assembleMenus($menus) {
        $model = array();
        foreach ($menus as $menu) {
            $dishes = $this->orderInteractor->readDishesByMenuId($menu->id);

            $model[] = array(
                'date' => $menu->getDate()->format('l, j.n.Y'),
                'dish' => $this->assembleDishes($dishes)
            );
        }
        return $model;
    }

    /**
     * @param Dish[] $dishes
     * @return array
     */
    private function assembleDishes($dishes) {
        $model = array();
        foreach ($dishes as $dish) {
            $model[] = array(
                'text' => array(
                    '_' => $dish->getText(),
                    'name' => "dish[{$dish->id}]"
                )
            );
        }
        return $model;
    }

}
