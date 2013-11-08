<?php
namespace rtens\lacarte\model\stores;

use DateTime;
use rtens\lacarte\model\Selection;
use watoki\collections\Set;

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

    /**
     * @return Set|Selection[]
     */
    public function readAll() {
        return $this->inflateAll($this->db->readAll('SELECT * FROM selections'));
    }

    public function create(Selection $selection) {
        $this->createEntity($selection, 'selections');
    }

    public function update(Selection $selection) {
        $this->updateEntity($selection, 'selections');
    }

    public function readByUserAndDate($userId, Datetime $date) {
        var_dump($this->inflateAll($this->db->readAll('
            SELECT * FROM
              (SELECT * FROM selections AS SEL
                    JOIN menus AS MEN ON SEL.menuId=MEN.id) AS SELMEN
                    JOIN dishes AS DIS ON SELMEN.menuId = DIS.menuId
                    WHERE SELMEN.userId = ?
                    AND SELMEN.date = ?
                    '
            , array($userId, $date->format('Y-m-d 00:00:00'))))
        );

    }

    public function readAllByDishId($dishId) {
        return $this->inflateAll($this->db->readAll('SELECT * FROM selections WHERE dishId = ?', array($dishId)));
    }
}