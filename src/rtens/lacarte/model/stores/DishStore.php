<?php
namespace rtens\lacarte\model\stores;
 
use rtens\lacarte\model\Dish;
use watoki\collections\Set;

class DishStore extends Store {

    public static $CLASS = __CLASS__;

    public function readAllByMenuId($id) {
        return $this->inflateAll($this->db->readAll('SELECT * FROM dishes WHERE menuId = ?', array($id)));
    }

    public function readAll() {
        return $this->inflateAll($this->db->readAll('SELECT * FROM dishes'));
    }

    public function readById($id) {
        return $this->inflate($this->db->readOne('SELECT * FROM dishes WHERE id = ?', array($id)));
    }

    protected function inflate($row) {
        $dish = new Dish($row['menuId'], $row['text']);
        $dish->id = $row['id'];
        return $dish;
    }

    public function create(Dish $dish) {
        $this->createEntity($dish, 'dishes');
    }

    public function update(Dish $dish) {
        $this->updateEntity($dish, 'dishes');
    }

    public function delete($dish) {
        $this->deleteEntity($dish, 'dishes');
    }

}
