<?php
namespace rtens\lacarte\model\stores;
 
use rtens\lacarte\model\Menu;
use watoki\collections\Liste;

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
        $menu->id = intval($row['id']);
        return $menu;
    }

    public function create(Menu $menu) {
        $this->createEntity($menu, 'menus');
    }

    public function readById($menuId) {
        return $this->inflate($this->db->readOne('SELECT * FROM menus WHERE id = ?', array($menuId)));
    }

    public function delete(Menu $menu) {
        $this->db->execute('DELETE from menus WHERE id = ?', array($menu->id));
    }

    public function readAll() {
        return $this->inflateAll($this->db->readAll('SELECT * FROM menus'));
    }

    public function readAllByDate(\DateTime $date) {
        return $this->inflateAll($this->db->readAll('SELECT * FROM menus WHERE date = ?',
            array($this->serialize($date))));
    }

}
