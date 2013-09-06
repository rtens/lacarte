<?php
namespace spec\rtens\lacarte\fixture\model;

use rtens\lacarte\model\Group;
use rtens\lacarte\model\stores\GroupStore;
use rtens\lacarte\model\stores\UserStore;
use rtens\lacarte\model\User;
use rtens\mockster\MockFactory;
use spec\rtens\lacarte\fixture\Fixture;
use spec\rtens\lacarte\TestCase;
use watoki\factory\Factory;

class UserFixture extends Fixture {

    public static $CLASS = __CLASS__;

    /** @var array|User[] */
    private $users = array();

    /** @var Group */
    private $group;

    /** @var UserStore */
    private $store;

    public function __construct(TestCase $test, Factory $factory, UserStore $store, GroupStore $groupStore) {
        parent::__construct($test, $factory);

        $this->store = $store;

        $this->group = new Group('Test', '', '');
        $groupStore->create($this->group);
    }

    public function thenThereShouldBe_Users($count) {
        $this->test->assertCount($count, $this->store->readAll());
    }

    public function givenTheUser($name) {
        $this->givenTheUser_WithTheEmail($name, strtolower(str_replace(' ', '.', $name)) . '@example.com');
    }

    public function getGroup() {
        return $this->group;
    }

    public function getUser($name) {
        return $this->users[$name];
    }

    public function givenTheUser_WithTheEmail($name, $email) {
        $this->givenTheUser_WithTheEmail_AndKey($name, $email, 'key_' . $email);
    }

    public function givenTheUser_WithTheEmail_AndKey($name, $email, $key) {
        $user = new User($this->group->id, $name, $email, $key);
        $this->store->create($user);

        $this->users[$name] = $user;
    }

    public function thenThereShouldBeAUserWithTheName_TheEmailAndTheKey($name, $email, $key) {
        foreach ($this->store->readAll() as $user) {
            if ($user->getName() == $name && $user->getEmail() == $email && $user->getKey() == $key) {
                return;
            }
        }
        $this->test->fail('User does not exist');
    }

    public function thenThereShouldBeAUserWithTheTheName($name) {
        foreach ($this->store->readAll() as $user) {
            if ($user->getName() == $name) {
                return;
            }
        }
        $this->test->fail('User does not exist');
    }

}