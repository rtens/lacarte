<?php
namespace rtens\lacarte;
 
use rtens\lacarte\core\Configuration;
use rtens\lacarte\model\Dish;
use rtens\lacarte\model\Group;
use rtens\lacarte\model\Menu;
use rtens\lacarte\model\Order;
use rtens\lacarte\model\Selection;
use rtens\lacarte\model\stores\DishStore;
use rtens\lacarte\model\stores\GroupStore;
use rtens\lacarte\model\stores\MenuStore;
use rtens\lacarte\model\stores\OrderStore;
use rtens\lacarte\model\stores\SelectionStore;
use rtens\lacarte\model\stores\UserStore;
use rtens\lacarte\utils\MailService;
use watoki\collections\Collection;
use watoki\collections\Liste;
use watoki\collections\Set;

class OrderInteractor {

    const DISH_PER_MENU = 3;

    public static $CLASS = __CLASS__;

    private $groupStore;

    private $mailService;

    private $userStore;

    private $selectionStore;

    private $orderStore;

    private $menuStore;

    private $dishStore;

    private $configuration;

    function __construct(OrderStore $orderStore, MenuStore $menuStore, DishStore $dishStore,
                         SelectionStore $selectionStore, UserStore $userStore, MailService $mailService,
                         GroupStore $groupStore, Configuration $configuration) {
        $this->orderStore = $orderStore;
        $this->menuStore = $menuStore;
        $this->dishStore = $dishStore;
        $this->selectionStore = $selectionStore;
        $this->userStore = $userStore;
        $this->mailService = $mailService;
        $this->groupStore = $groupStore;
        $this->configuration = $configuration;
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
     * @return \watoki\collections\Set|Order[]
     */
    public function readAll() {
        return $this->orderStore->readAllSortedByDeadline();
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

    /**
     * @param Collection|Dish[] $dishes
     */
    public function updateDishes(Collection $dishes) {
        $menuHasDish = array();
        $menus = array();

        foreach ($dishes as $dish) {
            $menus[$dish->getMenuId()] = true;

            if (!$dish->getText()) {
                $this->dishStore->delete($dish);
            } else {
                $menuHasDish[$dish->getMenuId()] = true;
                $this->dishStore->update($dish);
            }
        }

        foreach (array_keys($menus) as $menuId) {
            if (!isset($menuHasDish[$menuId])) {
                $menu = new Menu(1, new \DateTime());
                $menu->id = $menuId;
                $this->menuStore->delete($menu);
            }
        }
    }

    /**
     * @param Collection|Selection[] $selections
     */
    public function saveSelections(Collection $selections) {
        foreach ($selections as $selection) {
            if ($selection->id) {
                $this->selectionStore->update($selection);
            } else {
                $this->selectionStore->create($selection);
            }
        }
    }

    public function readById($id) {
        return $this->orderStore->readById($id);
    }

    public function readDishById($id) {
        return $this->dishStore->readById($id);
    }

    /**
     * @param int $menuId
     * @param int $userId
     * @return Selection
     */
    public function readSelectionByMenuIdAndUserId($menuId, $userId) {
        return $this->selectionStore->readByMenuIdAndUserId($menuId, $userId);
    }

    /**
     * @param $menuId
     * @return Menu
     */
    public function readMenuById($menuId) {
        return $this->menuStore->readById($menuId);
    }

    public function sendMail(Order $order, $subject, $body) {
        $group = $this->groupStore->readById($order->getGroupId());

        foreach ($this->userStore->readAllByGroup($group) as $user) {
            $userName = $user->getName();
            if (strpos($userName, ' ')) {
                list($userName, ) = explode(' ', $userName);
            }
            $data = array(
                'name' => $userName,
                'url' => $this->configuration->getHost()
                    . '/order/select.html?order='. $order->id . '&key=' . $user->getKey()
            );

            $replaceSubject = $subject;
            $replaceBody = $body;
            foreach ($data as $key => $value) {
                $replaceSubject = str_replace('{' . $key . '}', $value, $replaceSubject);
                $replaceBody = str_replace('{' . $key . '}', $value, $replaceBody);
            }

            $this->mailService->send($group->getAdminEmail(), $user->getEmail(), $replaceSubject, $replaceBody);
        }
    }

}
