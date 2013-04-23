<?php
namespace rtens\lacarte;
 
use rtens\lacarte\model\Dish;
use rtens\lacarte\model\Menu;
use rtens\lacarte\model\Order;
use rtens\lacarte\model\stores\DishStore;
use rtens\lacarte\model\stores\MenuStore;
use rtens\lacarte\model\stores\OrderStore;
use watoki\collections\Liste;
use watoki\collections\Set;

class OrderInteractor {

    const DISH_PER_MENU = 3;

    public static $CLASS = __CLASS__;

    private $orderStore;

    private $menuStore;

    private $dishStore;

    function __construct(OrderStore $orderStore, MenuStore $menuStore, DishStore $dishStore) {
        $this->orderStore = $orderStore;
        $this->menuStore = $menuStore;
        $this->dishStore = $dishStore;
    }

    /**
     * @param int $groupId
     * @param \DateTime $firstDay
     * @param \DateTime $lastDay
     * @param \DateTime $deadline
     * @throws \InvalidArgumentException
     * @return Order
     */
    public function createOrder($groupId, \DateTime $firstDay, \DateTime $lastDay, \DateTime $deadline) {
        if ($lastDay <= $firstDay) {
            throw new \InvalidArgumentException('First day must be before last day');
        } else if ($deadline > $firstDay) {
            throw new \InvalidArgumentException('Deadline must be before or on first day');
        }

        $name = $firstDay->format('d.m.Y') . ' - ' . $lastDay->format('d.m.Y');
        $order = new Order($groupId, $name, $deadline);
        $this->orderStore->create($order);

        $currentDay = clone $firstDay;
        while ($currentDay <= $lastDay) {
            if ($currentDay->format('N') < 6) {
                $menu = new Menu($order->id, clone $currentDay);
                $this->menuStore->create($menu);

                for ($i = 0; $i < self::DISH_PER_MENU; $i++) {
                    $this->dishStore->create(new Dish($menu->id, ''));
                }
            }
            $currentDay->add(new \DateInterval('P1D'));
        }

        return $order;
    }

    /**
     * @return Set|Order[]
     */
    public function readAll() {
        return $this->orderStore->readAll();
    }

    /**
     * @param $id
     * @return Liste|Menu[]
     */
    public function readMenusByOrderId($id) {
        return $this->menuStore->readAllByOrderId($id);
    }

    /**
     * @param $id
     * @return Set|Dish[]
     */
    public function readDishesByMenuId($id) {
        return $this->dishStore->readAllByMenuId($id);
    }

}
