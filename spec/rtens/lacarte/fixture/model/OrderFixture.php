<?php
namespace spec\rtens\lacarte\fixture\model;

use rtens\lacarte\model\Order;
use rtens\lacarte\model\stores\OrderStore;
use rtens\lacarte\OrderInteractor;
use rtens\mockster\MockFactory;
use spec\rtens\lacarte\fixture\Fixture;
use spec\rtens\lacarte\TestCase;
use watoki\factory\Factory;

class OrderFixture extends Fixture {

    public static $CLASS = __CLASS__;

    /** @var array|Order[] */
    private $orders = array();

    public function __construct(TestCase $test, Factory $factory, OrderStore $store, UserFixture $user) {
        parent::__construct($test, $factory);
        $this->store = $store;
        $this->user = $user;
    }

    public function givenTheOrder_WithDeadline($name, $deadline) {
        $order = new Order($this->user->getGroup()->id, $name, new \DateTime($deadline));
        $this->store->create($order);
        $this->orders[$name] = $order;
    }
}