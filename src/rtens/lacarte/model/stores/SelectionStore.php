<?php
namespace rtens\lacarte\model\stores;

use rtens\lacarte\model\Selection;

class SelectionStore extends Store {

    public function readByMenuIdAndUserId($menuId, $userId) {
        return $this->inflate($this->db->readOne('SELECT * FROM selections WHERE menuId = ? AND userId = ?',
            array($menuId, $userId)));
    }

    protected function inflate($row) {
        $selection = new Selection($row['userId'], $row['menuId'], $row['dishId']);
        $selection->id = intval($row['id']);
        return $selection;
    }

    public function readAll() {
        return $this->inflateAll($this->db->readAll('SELECT * FROM selections'));
    }

    public function create(Selection $selection) {
        $this->createEntity($selection, 'selections');
    }

    public function update(Selection $selection) {
        $this->updateEntity($selection, 'selections');
    }

    public function readAllByDishId($dishId) {
        return $this->inflateAll($this->db->readAll('SELECT * FROM selections WHERE dishId = ?', array($dishId)));
    }
}