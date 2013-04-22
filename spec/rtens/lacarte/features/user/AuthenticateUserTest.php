<?php
namespace spec\rtens\lacarte\features\user;
 
use rtens\lacarte\UserInteractor;
use rtens\lacarte\model\Group;
use rtens\lacarte\model\User;
use rtens\lacarte\model\stores\GroupStore;
use rtens\lacarte\model\stores\UserStore;
use spec\rtens\lacarte\Test;
use spec\rtens\lacarte\Test_Given;
use spec\rtens\lacarte\Test_Then;
use spec\rtens\lacarte\Test_When;

/**
 * @property AuthenticateUserTest_Given given
 * @property AuthenticateUserTest_When when
 * @property AuthenticateUserTest_Then then
 */
class AuthenticateUserTest extends Test {

    function testCorrectKey() {
        $this->given->theGroup('test');
        $this->given->theUser_WithTheKey('John', 'someKey');

        $this->when->iAuthenticateWithTheKey('someKey');

        $this->then->iShouldBeLoggedInForTheGroup('test');
    }

    function testIncorrectKey() {
        $this->given->theGroup('test');
        $this->given->theUser_WithTheKey('John', 'someKey');

        $this->when->iAuthenticateWithTheKey('wrongKey');

        $this->then->iShouldNotBeAuthenticated();
    }

}

/**
 * @property AuthenticateUserTest test
 */
class AuthenticateUserTest_Given extends Test_Given {

    /** @var Group */
    public $group;

    /** @var GroupStore */
    private $groupStore;

    /** @var UserStore */
    private $userStore;

    function __construct(Test $test) {
        parent::__construct($test);
        $this->groupStore = $test->factory->getInstance(GroupStore::$CLASS);
        $this->userStore = $test->factory->getInstance(UserStore::$CLASS);
    }

    public function theGroup($name) {
        $this->group = new Group($name, '', '');
        $this->groupStore->create($this->group);
    }

    public function theUser_WithTheKey($name, $key) {
        $this->userStore->create(new User($this->group->id, $name, $name . '@example.com', $key));
    }
}

/**
 * @property AuthenticateUserTest test
 */
class AuthenticateUserTest_When extends Test_When {

    /**
     * @var null|Group
     */
    public $group;

    /**
     * @var UserInteractor
     */
    private $userInteractor;

    function __construct(Test $test) {
        parent::__construct($test);
        $this->userInteractor = $test->factory->getInstance(UserInteractor::$CLASS);
    }

    public function iAuthenticateWithTheKey($key) {
        $this->group = $this->userInteractor->authorizeUser($key);
    }
}

/**
 * @property AuthenticateUserTest test
 */
class AuthenticateUserTest_Then extends Test_Then {

    public function iShouldBeLoggedInForTheGroup($name) {
        $this->test->assertNotNull($this->test->when->group, 'No group');
        $this->test->assertEquals($name, $this->test->when->group->getName());
    }

    public function iShouldNotBeAuthenticated() {
        $this->test->assertNull($this->test->when->group);
    }
}
