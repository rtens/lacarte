<?php
namespace rtens\lacarte\web\order;

use rtens\lacarte\core\NotFoundException;
use rtens\lacarte\model\Menu;
use rtens\lacarte\model\Order;
use rtens\lacarte\model\Selection;
use rtens\lacarte\Presenter;
use rtens\lacarte\web\DefaultResource;
use watoki\curir\http\Url;
use watoki\curir\Responder;
use watoki\curir\responder\Redirecter;

class SelectionResource extends DefaultResource {

    static $CLASS = __CLASS__;

    /** @var \rtens\lacarte\OrderInteractor <- */
    public $orderInteractor;

    /**
     * @param int $order ID of order to display
     * @return Responder
     */
    public function doGet($order) {
        if ($this->isAdmin()) {
            return new Redirecter(Url::parse('selections.html?order=' . $order));
        }

        try {
            return new Presenter($this, $this->assembleModel(array(
                'order' => $this->assembleOrder($this->orderInteractor->readById($order)),
                'error' => null
            )));
        } catch (NotFoundException $nfe) {
            return new Presenter($this, $this->assembleModel(array(
                'order' => null,
                'error' => 'You seem to have no selections for this order.'
            )));
        } catch (\Exception $e) {
            return new Presenter($this, $this->assembleModel(array(
                'order' => null,
                'error' => $e->getMessage()
            )));
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
                'notSelected' => $this->assembleNotSelectedDishTexts($menu, $selection),
                'action' => $this->assembleActions($order, $selection)
            );
        }
        return $selections;
    }

    private function assembleActions(Order $order, Selection $selection) {
        if (!$this->orderInteractor->canYield($selection->id, $this->getLoggedInUser())) {
            return null;
        }
        $href = '?order=' . $order->id . '&selection=' . $selection->id . '&method=yield';
        return array(
            'yield' => !$selection->isYielded() ? array('href' => $href) : null,
            'unyield' => $selection->isYielded() ? array('href' => $href . '&yielded=false') : null
        );
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

    /**
     * @param int $order
     * @param int $selection
     * @param bool $yielded
     * @return Presenter
     */
    public function doYield($order, $selection, $yielded = true) {
        if ($this->orderInteractor->canYield($selection, $this->getLoggedInUser())) {
            $this->orderInteractor->yieldSelection($selection, $yielded);
            $error = null;
        } else {
            $error = 'Could not update selection.';
        }

        return new Presenter($this, $this->assembleModel(array(
            'order' => $this->assembleOrder($this->orderInteractor->readById($order)),
            'error' => $error
        )));
    }

}