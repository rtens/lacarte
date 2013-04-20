<?php
namespace spec\rtens\lacarte\features;

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
 * @property CreateUserTest_Given given
 * @property CreateUserTest_When when
 * @property CreateUserTest_Then then
 */
class CreateUserTest extends Test {

    function testSuccess() {
        $this->given->theGroup('test');
        $this->given->theName('Marina');
        $this->given->theEmail('m@gnz.es');

        $this->when->iCreateANewUserForTheGroup();

        $this->then->theUserShouldBeCreated();
        $this->then->thereShouldBeAUser('Marina', 'm@gnz.es');
    }

    function testNotAdmin() {
        $this->markTestIncomplete();
    }

    function testEmptyName() {
        $this->markTestIncomplete();
    }

    function testEmptyEmail() {
        $this->markTestIncomplete();
    }

    function testAlreadyExistingEmail() {
        $this->markTestIncomplete();
    }

}

/**
 * @property CreateUserTest test
 */
class CreateUserTest_Given extends Test_Given {

    public $name;
    public $email;

    /**
     * @var Group
     */
    public $group;

    public function theName($name) {
        $this->name = $name;
    }

    public function theEmail($email) {
        $this->email = $email;
    }

    public function theGroup($name) {
        /** @var GroupStore $store */
        $store = $this->test->factory->getInstance(GroupStore::$CLASS);

        $this->group = new Group($name, '', '');
        $store->create($this->group);
    }
}

/**
 * @property CreateUserTest test
 */
class CreateUserTest_When extends Test_When {

    /**
     * @var User
     */
    public $user;

    public function iCreateANewUserForTheGroup() {
        /** @var UserInteractor $interactor */
        $interactor = $this->test->factory->getInstance(UserInteractor::$CLASS);
        $this->user = $interactor->createUser($this->test->given->group,
            $this->test->given->name, $this->test->given->email);
    }
}

/**
 * @property CreateUserTest test
 */
class CreateUserTest_Then extends Test_Then {

    public function thereShouldBeAUser($name, $email) {
        /** @var UserStore $store */
        $store = $this->test->factory->getInstance(UserStore::$CLASS);
        $user = $store->readByEmail($email);
        $this->test->assertEquals($name, $user->getName());
    }

    public function theUserShouldBeCreated() {
        $this->test->assertNotNull($this->test->when->user);
    }
}