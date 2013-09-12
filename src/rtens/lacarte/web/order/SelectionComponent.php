<?php
namespace rtens\lacarte\web\order;

use rtens\lacarte\core\NotFoundException;
use rtens\lacarte\model\Menu;
use rtens\lacarte\model\Selection;
use rtens\lacarte\OrderInteractor;
use rtens\lacarte\UserInteractor;
use rtens\lacarte\core\Session;
use rtens\lacarte\model\Order;
use rtens\lacarte\web\DefaultComponent;
use watoki\curir\Path;
use watoki\curir\Url;
use watoki\curir\controller\Module;
use watoki\factory\Factory;

class SelectionComponent extends DefaultComponent {

    static $CLASS = __CLASS__;

    private $orderInteractor;

    function __construct(Factory $factory, Path $route, Module $parent = null,
                         UserInteractor $userInteractor, Session $session, OrderInteractor $orderInteractor) {
        parent::__construct($factory, $route, $parent, $userInteractor, $session);
        $this->orderInteractor = $orderInteractor;
    }

    /**
     * @param int $order ID of order to display
     * @return array
     */
    public function doGet($order) {
        if ($this->isAdmin()) {
            return $this->redirect(Url::parse('selections.html?order=' . $order));
        }

        try {
            return $this->assembleModel(array(
                'order' => $this->assembleOrder($this->orderInteractor->readById($order)),
                'error' => null
            ));
        } catch (NotFoundException $nfe) {
            return $this->assembleModel(array(
                'order' => null,
                'error' => 'You seem to have no selections for this order.'
            ));
        } catch (\Exception $e) {
            return $this->assembleModel(array(
                'order' => null,
                'error' => $e->getMessage()
            ));
        }
    }

    private function assembleOrder(Order $order) {
        return array(
            'name' => $order->getName(),
            'selection' => $this->assembleSelections($order),
        );
    }

    private function assembleSelections(Order $order) {
        $selections = array();
        foreach ($this->orderInteractor->readMenusByOrderId($order->id) as $menu) {
            $selection = $this->orderInteractor->readSelectionByMenuIdAndUserId($menu->id, $this->getLoggedInUser()->id);

            $selections[] = array(
                'date' => $menu->getDate()->format('l, j.n.Y'),
                'dish' => $this->getSelectedDishText($selection),
                'notSelected' => $this->assembleNotSelectedDishTexts($menu, $selection)
            );
        }
        return $selections;
    }

    /**
     * @param Selection $selection
     * @return string
     */
    private function getSelectedDishText(Selection $selection) {
        if ($selection->hasDish()) {
            return $this->orderInteractor->readDishById($selection->getDishId())->getText();
        } else {
            return 'You selected no dish';
        }
    }

    /**
     * @param Menu $menu
     * @param Selection $selection
     * @return array|string[]
     */
    private function assembleNotSelectedDishTexts(Menu $menu, Selection $selection) {
        $notSelected = array();
        foreach ($this->orderInteractor->readDishesByMenuId($menu->id) as $dish) {
            if ($dish->id != $selection->getDishId()) {
                $notSelected[] = $dish->getText();
            }
        }
        return $notSelected;
    }

}