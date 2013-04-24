<?php
namespace rtens\lacarte\model\stores;
 
use rtens\lacarte\model\Menu;
use watoki\collections\Liste;
use watoki\collections\Set;

class MenuStore extends Store {

    public static $CLASS = __CLASS__;

    public function readAllByOrderId($id) {
        $menus = new Liste();
        foreach ($this->db->readAll('SELECT * FROM menus WHERE orderId = ? ORDER BY date ASC', array($id)) as $row) {
            $menus->append($this->inflate($row));
        }
        return $menus;
    }

    protected function inflate($row) {
        $menu = new Menu($row['orderId'], new \DateTime($row['date']));
        $menu->id = $row['id'];
        return $menu;
    }

    public function create(Menu $menu) {
        $this->createEntity($menu, 'menus');
    }

}
