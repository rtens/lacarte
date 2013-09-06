<?php
namespace spec\rtens\lacarte\fixture\model;

use rtens\lacarte\model\stores\UserStore;
use spec\rtens\lacarte\fixture\Fixture;

class UserFixture extends Fixture {

    public static $CLASS = __CLASS__;

    /** @var UserStore */
    private $store;

    public function setUp() {
        parent::setUp();

        $this->store = $this->factory->getInstance(UserStore::$CLASS);
    }

    public function thenThereShouldBe_Users($count) {
        $this->test->assertCount($count, $this->store->readAll());
    }

    public function thenThereShouldBeAUserWithTheName_TheEmailAndAKey($name, $email) {
        foreach ($this->store->readAll() as $user) {
            if ($user->getName() == $name && $user->getEmail() == $email && $user->getKey()) {
                return;
            }
        }
        $this->test->fail('User does not exist');
    }

}