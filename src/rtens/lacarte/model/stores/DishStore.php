<?php
namespace rtens\lacarte\model\stores;
 
use rtens\lacarte\model\Dish;
use watoki\collections\Set;

class DishStore extends Store {

    public static $CLASS = __CLASS__;

    public function readAllByMenuId($id) {
        $dishes = new Set();
        foreach ($this->db->readAll('SELECT * FROM dishes WHERE menuId = ?', array($id)) as $row) {
            $dishes->put($this->inflate($row));
        }
        return $dishes;
    }

    private function inflate($row) {
        $dish = new Dish($row['menuId'], $row['text']);
        $dish->id = $row['id'];
        return $dish;
    }

    public function create(Dish $dish) {
        $this->createEntity($dish, 'dishes');
    }

}
