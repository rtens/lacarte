<?php
namespace rtens\lacarte;
 
use rtens\lacarte\model\Order;

class OrderInteractor {

    public static $CLASS = __CLASS__;

    /**
     * @param \DateTime $firstDay
     * @param \DateTime $lastDay
     * @param \DateTime $deadline
     * @return Order
     */
    public function createOrder(\DateTime $firstDay, \DateTime $lastDay, \DateTime $deadline) {
        return new Order;
    }

}
