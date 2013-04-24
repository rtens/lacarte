<?php
namespace rtens\lacarte\model\stores;
 
use rtens\lacarte\model\Order;
use watoki\collections\Set;

class OrderStore extends Store {

    public static $CLASS = __CLASS__;

    public function create($order) {
        $this->createEntity($order, 'orders');
    }

    public function readAll() {
        return $this->inflateAll($this->db->readAll('SELECT * FROM orders'));
    }

    protected function inflate($row) {
        $order = new Order($row['groupId'], $row['name'], new \DateTime($row['deadline']));
        $order->id = $row['id'];
        return $order;
    }

    public function readById($id) {
        return $this->inflate($this->db->readOne('SELECT * FROM orders WHERE id = ?', array($id)));
    }

    public function readAllSortedByDeadline() {
        return $this->inflateAll($this->db->readAll('SELECT * FROM orders ORDER BY deadline DESC'));
    }

}
