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
        $orders = new Set();
        foreach ($this->db->readAll('SELECT * FROM orders') as $row) {
            $orders->put($this->inflate($row));
        }
        return $orders;
    }

    private function inflate($row) {
        $order = new Order($row['groupId'],
            $row['name'],
            new \DateTime($row['deadline']));
        $order->id = $row['id'];
        return $order;
    }

}
