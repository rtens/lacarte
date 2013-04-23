<?php
namespace rtens\lacarte\model;

class Menu {

    public $id;

    /** @var int */
    private $orderId;

    /** @var \DateTime */
    private $date;

    function __construct($orderId, \DateTime $date) {
        $this->orderId = $orderId;
        $this->date = $date;
    }

    /**
     * @return int
     */
    public function getOrderId() {
        return $this->orderId;
    }

    /**
     * @return \DateTime
     */
    public function getDate() {
        return $this->date;
    }

}
