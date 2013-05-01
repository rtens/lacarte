<?php
namespace spec\rtens\lacarte\features\user;

use rtens\lacarte\UserInteractor;
use rtens\lacarte\model\User;
use rtens\lacarte\model\stores\UserStore;
use spec\rtens\lacarte\Test;
use spec\rtens\lacarte\Test_Given;
use spec\rtens\lacarte\Test_Then;
use spec\rtens\lacarte\Test_When;

/**
 * @property DeleteUserTest_Given given
 * @property DeleteUserTest_When when
 * @property DeleteUserTest_Then then
 */
class DeleteUserTest extends Test {

    function testDeleteUserWithoutRelations() {
        $this->given->theUser('Test');
        $this->given->theUser('Mo');

        $this->when->iDeleteTheUser('Test');

        $this->then->thereShouldBe_User(1);
    }

}

class DeleteUserTest_Given extends Test_Given {

    /** @var array|User[] */
    public $users = array();

    function __construct(Test $test, UserStore $store) {
        parent::__construct($test);
        $this->store = $store;
    }

    public function theUser($name) {
        $this->users[$name] = new User(1, $name, "$name@test", "123$name");
        $this->store->create($this->users[$name]);
    }
}

/**
 * @property DeleteUserTest test
 */
class DeleteUserTest_When extends Test_When {

    function __construct(Test $test, UserInteractor $interactor) {
        parent::__construct($test);
        $this->interactor = $interactor;
    }

    public function iDeleteTheUser($name) {
        $this->interactor->delete($this->test->given->users[$name]);
    }
}

/**
 * @property DeleteUserTest test
 */
class DeleteUserTest_Then extends Test_Then {

    public function thereShouldBe_User($count) {
        $this->test->assertEquals($count, $this->test->given->store->readAll()->count());
    }
}